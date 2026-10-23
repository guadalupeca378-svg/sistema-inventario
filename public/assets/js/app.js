/*
 * Papelería Los Pingüinos · comportamiento de la interfaz (sin dependencias).
 *  - Snackbar: se oculta solo a los ~5 s (o al pulsar cerrar).
 *  - Filtro en vivo: <input data-filtrar="#idTabla"> filtra las filas del <tbody>.
 *  - Confirmación: <form data-confirmar="..."> o <button data-confirmar="..."> abre un
 *    <dialog> Material propio con Cancelar / Eliminar antes de enviar.
 */
(function () {
  'use strict';

  /* ---------- Snackbar ---------- */
  function iniciarSnackbars() {
    document.querySelectorAll('.md-snackbar').forEach(function (barra) {
      var temporizador = null;
      var ocultar = function () {
        if (barra.classList.contains('is-hiding')) return;
        barra.classList.add('is-hiding');
        setTimeout(function () { barra.remove(); }, 300);
      };
      var programar = function () { temporizador = setTimeout(ocultar, 5000); };
      var cancelar = function () { clearTimeout(temporizador); };

      var cerrar = barra.querySelector('.md-snackbar__close');
      if (cerrar) cerrar.addEventListener('click', ocultar);

      // Pausar mientras el usuario lo lee o lo enfoca.
      barra.addEventListener('mouseenter', cancelar);
      barra.addEventListener('mouseleave', programar);
      barra.addEventListener('focusin', cancelar);
      barra.addEventListener('focusout', programar);
      programar();
    });
  }

  /* ---------- Filtro de tablas ---------- */
  function normalizar(texto) {
    return (texto || '')
      .toString()
      .toLowerCase()
      .normalize('NFD')
      .replace(/[̀-ͯ]/g, '')
      .trim();
  }

  function iniciarFiltros() {
    document.querySelectorAll('input[data-filtrar]').forEach(function (entrada) {
      var tabla = document.querySelector(entrada.getAttribute('data-filtrar'));
      if (!tabla) return;
      var cuerpo = tabla.tBodies[0] || tabla;
      var columnas = tabla.rows[0] ? tabla.rows[0].cells.length : 1;

      var filaVacia = document.createElement('tr');
      filaVacia.className = 'md-table__empty';
      filaVacia.hidden = true;
      var celda = document.createElement('td');
      celda.colSpan = columnas;
      celda.textContent = 'Ningún registro coincide con la búsqueda.';
      filaVacia.appendChild(celda);
      cuerpo.appendChild(filaVacia);

      var aviso = document.createElement('span');
      aviso.className = 'md-sr-only';
      aviso.setAttribute('role', 'status');
      entrada.insertAdjacentElement('afterend', aviso);

      var filtrar = function () {
        var termino = normalizar(entrada.value);
        var visibles = 0;
        Array.prototype.forEach.call(cuerpo.rows, function (fila) {
          if (fila === filaVacia) return;
          var coincide = termino === '' || normalizar(fila.textContent).indexOf(termino) !== -1;
          fila.hidden = !coincide;
          if (coincide) visibles++;
        });
        filaVacia.hidden = visibles !== 0;
        aviso.textContent = termino === '' ? '' : visibles + (visibles === 1 ? ' resultado' : ' resultados');
      };

      entrada.addEventListener('input', filtrar);
      entrada.addEventListener('search', filtrar);
      if (entrada.value) filtrar();
    });
  }

  /* ---------- Diálogo de confirmación ---------- */
  var dialogo = null;

  function crearDialogo() {
    if (dialogo) return dialogo;
    dialogo = document.createElement('dialog');
    dialogo.className = 'md-dialog';
    dialogo.setAttribute('aria-labelledby', 'md-dialog-titulo');
    dialogo.setAttribute('aria-describedby', 'md-dialog-texto');
    dialogo.innerHTML =
      '<form method="dialog">' +
      '<span class="material-symbols-rounded md-dialog__icon" aria-hidden="true">delete_forever</span>' +
      '<h2 class="md-dialog__title" id="md-dialog-titulo">¿Confirmar acción?</h2>' +
      '<p class="md-dialog__text" id="md-dialog-texto"></p>' +
      '<div class="md-dialog__actions">' +
      '<button type="submit" value="cancelar" class="md-btn md-btn--text" autofocus>Cancelar</button>' +
      '<button type="submit" value="aceptar" class="md-btn md-btn--danger">Eliminar</button>' +
      '</div></form>';
    document.body.appendChild(dialogo);
    return dialogo;
  }

  function confirmar(mensaje, etiquetaAceptar) {
    return new Promise(function (resolver) {
      var d = crearDialogo();
      d.querySelector('.md-dialog__text').textContent = mensaje;
      d.querySelector('button[value="aceptar"]').textContent = etiquetaAceptar || 'Eliminar';
      d.returnValue = '';
      var alCerrar = function () {
        d.removeEventListener('close', alCerrar);
        resolver(d.returnValue === 'aceptar');
      };
      d.addEventListener('close', alCerrar);
      if (typeof d.showModal === 'function') {
        d.showModal();
      } else {
        // Navegadores sin <dialog>: degradar a confirm nativo.
        d.removeEventListener('close', alCerrar);
        resolver(window.confirm(mensaje));
      }
    });
  }

  function iniciarConfirmaciones() {
    // Botones con data-confirmar (dentro de un formulario).
    document.addEventListener('click', function (evento) {
      var boton = evento.target.closest('button[data-confirmar], input[type="submit"][data-confirmar]');
      if (!boton || !boton.form || boton.dataset.confirmado === '1') return;
      evento.preventDefault();
      confirmar(boton.getAttribute('data-confirmar'), boton.getAttribute('data-confirmar-boton')).then(function (ok) {
        if (!ok) return;
        boton.dataset.confirmado = '1';
        boton.form.dataset.confirmado = '1';
        enviar(boton.form, boton);
        delete boton.dataset.confirmado;
        delete boton.form.dataset.confirmado;
      });
    });

    // Formularios con data-confirmar.
    document.addEventListener('submit', function (evento) {
      var form = evento.target;
      if (!form.matches('form[data-confirmar]')) return;
      if (form.dataset.confirmado === '1') { delete form.dataset.confirmado; return; }
      evento.preventDefault();
      var emisor = evento.submitter || null;
      confirmar(form.getAttribute('data-confirmar'), form.getAttribute('data-confirmar-boton')).then(function (ok) {
        if (!ok) return;
        form.dataset.confirmado = '1';
        enviar(form, emisor);
      });
    }, true);
  }

  function enviar(form, emisor) {
    if (typeof form.requestSubmit === 'function') {
      form.requestSubmit(emisor && emisor.form === form ? emisor : undefined);
    } else {
      if (emisor && emisor.name) {
        var oculto = document.createElement('input');
        oculto.type = 'hidden';
        oculto.name = emisor.name;
        oculto.value = emisor.value;
        form.appendChild(oculto);
      }
      form.submit();
    }
  }

  function iniciar() {
    iniciarSnackbars();
    iniciarFiltros();
    iniciarConfirmaciones();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', iniciar);
  } else {
    iniciar();
  }
})();
