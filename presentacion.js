// Presentación de pruebas: ejecuta los 5 tipos de prueba del sistema, uno tras otro, con un
// encabezado por tipo y un resumen final. Uso: npm run presentacion  (o con pausas entre tipos:
// npm run presentacion:pausas, avanza con Enter).
const { spawnSync } = require('child_process');
const readline = require('readline');

const PAUSAS = process.argv.includes('--pausas');
const c = (codigo, texto) => `\x1b[${codigo}m${texto}\x1b[0m`;
const PHPUNIT = 'php vendor/bin/phpunit --testdox --colors=always';

const TIPOS = [
  {
    nombre: 'Pruebas unitarias',
    que: 'Cada función por separado: productos, movimientos y validaciones.',
    herramienta: 'PHPUnit 11',
    pasos: [`${PHPUNIT} --group unitaria`],
  },
  {
    nombre: 'Pruebas de integración',
    que: 'Módulos trabajando juntos: movimientos + reporte y guardado en JSON.',
    herramienta: 'PHPUnit 11',
    pasos: [`${PHPUNIT} --group integracion`],
  },
  {
    nombre: 'Pruebas de sistema',
    que: 'El sistema completo en el navegador, como lo usa la papelería.',
    herramienta: 'Playwright + Microsoft Edge',
    pasos: ['npx playwright test inventario.spec.js --headed'],
  },
  {
    nombre: 'Prueba de aceptación',
    que: 'Lista de verificación de la cliente: un día normal en la papelería.',
    herramienta: 'Playwright + Microsoft Edge',
    pasos: ['npx playwright test aceptacion.spec.js --headed'],
  },
  {
    nombre: 'Prueba de regresión',
    que: 'Se vuelve a correr TODO para confirmar que las correcciones no descompusieron nada.',
    herramienta: 'PHPUnit 11 + Playwright',
    pasos: [PHPUNIT, 'npx playwright test'],
    entorno: { LENTO: '0' },
  },
];

function esperarEnter(texto) {
  return new Promise((resolver) => {
    const rl = readline.createInterface({ input: process.stdin, output: process.stdout });
    rl.question(texto, () => { rl.close(); resolver(); });
  });
}

function encabezado(n, tipo) {
  const linea = '═'.repeat(72);
  console.log('\n' + c('36', linea));
  console.log(c('1;36', `  ${n}/${TIPOS.length} · ${tipo.nombre.toUpperCase()}`));
  console.log(`  ${tipo.que}`);
  console.log(c('2', `  Herramienta: ${tipo.herramienta}`));
  console.log(c('36', linea) + '\n');
}

(async () => {
  console.log(c('1;33', '\n  🐧 PAPELERÍA LOS PINGÜINOS · PRUEBAS AUTOMATIZADAS DEL SISTEMA DE INVENTARIO'));
  const resultados = [];

  for (const [i, tipo] of TIPOS.entries()) {
    if (PAUSAS && i > 0) await esperarEnter(c('33', '\n  Presiona Enter para continuar con el siguiente tipo de prueba… '));
    encabezado(i + 1, tipo);
    const inicio = Date.now();
    let ok = true;
    for (const comando of tipo.pasos) {
      console.log(c('2', `  $ ${comando}\n`));
      const r = spawnSync(comando, { shell: true, stdio: 'inherit', env: { ...process.env, ...tipo.entorno } });
      if (r.status !== 0) ok = false;
    }
    resultados.push({ tipo: tipo.nombre, ok, segundos: Math.round((Date.now() - inicio) / 1000) });
  }

  console.log('\n' + c('1;33', '  RESUMEN'));
  for (const r of resultados) {
    const estado = r.ok ? c('1;32', '✔ APROBADA') : c('1;31', '✘ FALLIDA ');
    console.log(`  ${estado}  ${r.tipo.padEnd(26)} ${c('2', `${r.segundos} s`)}`);
  }
  const todas = resultados.every((r) => r.ok);
  console.log('\n  ' + (todas ? c('1;32', 'Todas las pruebas pasaron.') : c('1;31', 'Hay pruebas fallidas: revisa arriba.')));
  console.log(c('2', '  Reporte con capturas de cada caso: npm run reporte\n'));
  process.exit(todas ? 0 : 1);
})();
