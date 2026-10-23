// @ts-check
const { defineConfig } = require('@playwright/test');

// Inventario de prueba aparte: las pruebas nunca tocan data/inventario.json.
const DATOS_DEMO = require('path').join(__dirname, 'data', 'demo-e2e.json');

module.exports = defineConfig({
  testDir: './e2e',
  timeout: 120_000,
  workers: 1,
  reporter: [['list'], ['html', { open: 'never', outputFolder: 'reporte-e2e' }]],
  use: {
    baseURL: 'http://localhost:8001',
    channel: 'msedge', // usa el Edge instalado; no descarga navegadores
    viewport: { width: 1366, height: 800 },
    launchOptions: { slowMo: Number(process.env.LENTO ?? 450) },
    video: 'retain-on-failure',
  },
  webServer: {
    command: `node e2e/preparar.js "${DATOS_DEMO}" && php -S localhost:8001 -t public`,
    url: 'http://localhost:8001/?p=salud',
    env: { INVENTARIO_DATOS: DATOS_DEMO },
    reuseExistingServer: false,
    stdout: 'ignore',
    stderr: 'ignore',
  },
});
