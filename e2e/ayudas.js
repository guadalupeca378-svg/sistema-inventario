// @ts-check
// Ayudas compartidas por las pruebas en navegador: letrero del caso en pantalla, escritura
// visible y lectura de avisos (snackbar).
const { expect } = require('@playwright/test');

/** @param {() => import('@playwright/test').Page} obtenerPagina */
function crearAyudas(obtenerPagina) {
  let letrero = '';

  // Letrero fijo arriba de la pantalla con el caso que se está ejecutando.
  async function mostrarLetrero() {
    await obtenerPagina().evaluate((texto) => {
      let el = document.getElementById('letrero-prueba');
      if (!el) {
        el = document.createElement('div');
        el.id = 'letrero-prueba';
        el.style.cssText = 'position:fixed;top:12px;left:50%;transform:translateX(-50%);z-index:99999;' +
          'background:#0F1B2D;color:#fff;border:3px solid #FFB547;border-radius:999px;padding:10px 26px;' +
          'font:600 20px/1.2 Roboto,system-ui,sans-serif;box-shadow:0 6px 24px rgba(0,0,0,.35);pointer-events:none';
        document.body.appendChild(el);
      }
      el.textContent = '🐧 ' + texto;
    }, letrero).catch(() => {});
  }

  async function caso(texto) {
    letrero = texto;
    await mostrarLetrero();
  }

  async function escribir(selector, texto) {
    const campo = obtenerPagina().locator(selector);
    await campo.fill('');
    await campo.pressSequentially(texto, { delay: 70 });
  }

  async function avisoDice(texto, tipo = 'exito') {
    const aviso = obtenerPagina().locator('.md-snackbar');
    await expect(aviso).toContainText(texto);
    if (tipo === 'error') await expect(aviso).toHaveClass(/md-snackbar--error/);
    await obtenerPagina().waitForTimeout(1200); // tiempo para que la clase lea el aviso
  }

  return { mostrarLetrero, caso, escribir, avisoDice };
}

module.exports = { crearAyudas };
