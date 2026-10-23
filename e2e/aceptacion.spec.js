// @ts-check
/**
 * CP-23 · Prueba de aceptación automatizada: recorre la lista de verificación de la cliente
 * (un día normal en Papelería Los Pingüinos) de principio a fin en un navegador real.
 * No sustituye la firma de conformidad de la cliente (CP-21).
 */
const { test, expect } = require('@playwright/test');
const { crearAyudas } = require('./ayudas');

const CLAVE = 'PAP-300';
const NOMBRE = 'Cuaderno Pingüino forma francesa';
const CLIENTE = 'Flor Yazmín Cordero Estañol (Cliente)';

/** @type {import('@playwright/test').Page} */
let page;
const { mostrarLetrero, caso, escribir, avisoDice } = crearAyudas(() => page);

async function movimiento(tipo, cantidad) {
  await page.goto('/?p=movimientos');
  await page.locator('.md-segmented label', { hasText: tipo }).click();
  await page.selectOption('#clave', CLAVE);
  await escribir('#cantidad', String(cantidad));
  await escribir('#responsable', CLIENTE);
  await page.locator('form.md-form button[type=submit]').click();
}

test.describe.configure({ mode: 'serial' });

test.beforeAll(async ({ browser }) => {
  page = await browser.newPage();
  page.on('load', mostrarLetrero);
});

test.afterEach(async ({}, testInfo) => {
  await testInfo.attach('pantalla', { body: await page.screenshot(), contentType: 'image/png' });
});

test.afterAll(async () => {
  await caso('✅ Lista de aceptación completa');
  await page.waitForTimeout(2500);
  await page.close();
});

test('CP-23 · 1 La cliente registra un producto nuevo', async () => {
  await page.goto('/?p=producto-nuevo');
  await caso('Aceptación 1/6 · Registrar producto nuevo');
  await escribir('#clave', CLAVE);
  await escribir('#nombre', NOMBRE);
  await escribir('#categoria', 'Escolar');
  await escribir('#existenciaMinima', '10');
  await page.locator('form.md-form button[type=submit]').click();
  await avisoDice(`Producto ${CLAVE} registrado`);
});

test('CP-23 · 2 Llega mercancía: entrada de 25 piezas', async () => {
  await caso('Aceptación 2/6 · Entrada de 25 piezas');
  await movimiento('Entrada', 25);
  await avisoDice('Existencia actual: 25');
});

test('CP-23 · 3 Venta: salida de 8 piezas', async () => {
  await caso('Aceptación 3/6 · Venta de 8 piezas');
  await movimiento('Salida', 8);
  await avisoDice('Existencia actual: 17');
});

test('CP-23 · 4 No se puede vender más de lo que hay', async () => {
  await caso('Aceptación 4/6 · Vender 30 (solo hay 17) → rechazo');
  await movimiento('Salida', 30);
  await avisoDice('Existencia insuficiente', 'error');
});

test('CP-23 · 5 Existencias avisan cuando un producto queda bajo el mínimo', async () => {
  await caso('Aceptación 5/6 · Venta de 10 → queda bajo el mínimo');
  await movimiento('Salida', 10);
  await avisoDice('Existencia actual: 7');
  await page.goto('/?p=existencias');
  const fila = page.locator('tr', { hasText: CLAVE });
  await fila.scrollIntoViewIfNeeded();
  await expect(fila).toContainText('7');
  await expect(fila).toContainText('Bajo mínimo');
  await page.waitForTimeout(1500);
});

test('CP-23 · 6 El reporte cuadra con lo que se movió', async () => {
  await caso('Aceptación 6/6 · Reporte: entradas 25, salidas 18, existencia 7');
  await page.goto('/?p=reporte');
  const celdas = page.locator('tr', { hasText: CLAVE }).locator('td');
  await celdas.first().scrollIntoViewIfNeeded();
  await expect(celdas.nth(2)).toHaveText('25');
  await expect(celdas.nth(3)).toHaveText('18');
  await expect(celdas.nth(4)).toHaveText('7');
  await page.waitForTimeout(1500);
});
