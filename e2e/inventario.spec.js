// @ts-check
/**
 * Pruebas funcionales de punta a punta: un navegador real recorre los casos de la matriz
 * de trazabilidad igual que lo haría un usuario de Papelería Los Pingüinos.
 * Demostración en clase: `npm run demo` (navegador visible y en cámara lenta).
 */
const { test, expect } = require('@playwright/test');
const { crearAyudas } = require('./ayudas');

const CLAVE = 'PAP-100';
const NOMBRE = 'Libreta Pingüino pasta dura';

/** @type {import('@playwright/test').Page} */
let page;
const { mostrarLetrero, caso, escribir, avisoDice } = crearAyudas(() => page);

test.describe.configure({ mode: 'serial' });

test.beforeAll(async ({ browser }) => {
  page = await browser.newPage();
  page.on('load', mostrarLetrero);
});

// Captura de cada caso como evidencia en el reporte HTML.
test.afterEach(async ({}, testInfo) => {
  await testInfo.attach('pantalla', { body: await page.screenshot(), contentType: 'image/png' });
});

test.afterAll(async () => {
  await caso('✅ Pruebas terminadas');
  await page.waitForTimeout(2500);
  await page.close();
});

test('CP-01 Registrar producto con datos válidos', async () => {
  await page.goto('/?p=productos');
  await caso('CP-01 · Registrar producto con datos válidos');
  await page.getByRole('link', { name: /Nuevo producto/ }).first().click();
  await escribir('#clave', CLAVE);
  await escribir('#nombre', NOMBRE);
  await escribir('#categoria', 'Escolar');
  await escribir('#existenciaMinima', '10');
  await page.locator('form.md-form button[type=submit]').click();
  await avisoDice(`Producto ${CLAVE} registrado`);
});

test('CP-02 Registrar producto con código duplicado muestra error', async () => {
  await caso('CP-02 · Clave duplicada → debe mostrar error');
  await page.goto('/?p=producto-nuevo');
  await escribir('#clave', CLAVE);
  await escribir('#nombre', 'Producto repetido');
  await escribir('#categoria', 'Escolar');
  await page.locator('form.md-form button[type=submit]').click();
  await avisoDice('ya existe', 'error');
});

test('CP-03 Consultar producto existente con el buscador', async () => {
  await caso('CP-03 · Buscar y consultar el producto');
  await page.goto('/?p=productos');
  await page.locator('input[data-filtrar]').pressSequentially('Libreta', { delay: 120 });
  const fila = page.locator('#tablaProductos tbody tr', { hasText: CLAVE });
  await expect(fila).toBeVisible();
  await expect(fila).toContainText(NOMBRE);
  await page.waitForTimeout(1200);
});

test('CP-04 Consultar producto inexistente muestra mensaje', async () => {
  await caso('CP-04 · Producto inexistente → mensaje');
  await page.goto('/?p=producto-editar&clave=PAP-999');
  await avisoDice('PAP-999 no encontrado', 'error');
});

test('CP-05 Modificar producto con datos válidos', async () => {
  await caso('CP-05 · Modificar producto');
  await page.goto('/?p=productos');
  await page.getByRole('link', { name: `Editar ${CLAVE}` }).click();
  await escribir('#nombre', `${NOMBRE} azul`);
  await escribir('#existenciaMinima', '12');
  await page.locator('form.md-form button[type=submit]').click();
  await avisoDice(`Producto ${CLAVE} actualizado`);
});

test('CP-07 Entrada de producto incrementa la existencia', async () => {
  await caso('CP-07 · Entrada de 20 piezas');
  await page.goto('/?p=movimientos');
  await page.locator('.md-segmented label', { hasText: 'Entrada' }).click();
  await page.selectOption('#clave', CLAVE);
  await escribir('#cantidad', '20');
  await escribir('#responsable', 'Karla Madrazo (Tester)');
  await page.locator('form.md-form button[type=submit]').click();
  await avisoDice(CLAVE);
});

test('CP-08 Salida de producto reduce la existencia', async () => {
  await caso('CP-08 · Salida de 5 piezas');
  await page.locator('.md-segmented label', { hasText: 'Salida' }).click();
  await page.selectOption('#clave', CLAVE);
  await escribir('#cantidad', '5');
  await page.locator('form.md-form button[type=submit]').click();
  await avisoDice(CLAVE);
});

test('CP-09 Salida mayor a la existencia se rechaza', async () => {
  await caso('CP-09 · Salida de 500 (solo hay 15) → rechazo');
  await page.locator('.md-segmented label', { hasText: 'Salida' }).click();
  await page.selectOption('#clave', CLAVE);
  await escribir('#cantidad', '500');
  await page.locator('form.md-form button[type=submit]').click();
  await avisoDice('Existencia insuficiente', 'error');
});

test('CP-10 Cantidad en cero no se acepta', async () => {
  await caso('CP-10 · Cantidad 0 → el formulario no la acepta');
  await page.goto('/?p=movimientos');
  await page.selectOption('#clave', CLAVE);
  await escribir('#cantidad', '0');
  await page.locator('form.md-form button[type=submit]').click();
  const valido = await page.locator('#cantidad').evaluate((el) => /** @type {HTMLInputElement} */ (el).checkValidity());
  expect(valido).toBe(false);
  await expect(page).toHaveURL(/p=movimientos/);
  await page.waitForTimeout(1500);
});

test('CP-11 Consultar existencias indica el estado', async () => {
  await caso('CP-11 · Existencias: 15 piezas, mínimo 12 → Normal');
  await page.goto('/?p=existencias');
  const fila = page.locator('tr', { hasText: CLAVE });
  await fila.scrollIntoViewIfNeeded();
  await expect(fila).toContainText('15');
  await expect(fila).toContainText('Normal');
  await page.waitForTimeout(1500);
});

test('CP-12 Reporte coincide con los movimientos', async () => {
  await caso('CP-12 · Reporte: entradas 20, salidas 5, existencia 15');
  await page.goto('/?p=reporte');
  const celdas = page.locator('tr', { hasText: CLAVE }).locator('td');
  await celdas.first().scrollIntoViewIfNeeded();
  await expect(celdas.nth(2)).toHaveText('20');
  await expect(celdas.nth(3)).toHaveText('5');
  await expect(celdas.nth(4)).toHaveText('15');
  await page.waitForTimeout(1500);
});

test('CP-06 Eliminar producto existente', async () => {
  await caso('CP-06 · Eliminar producto (con confirmación)');
  await page.goto('/?p=productos');
  await page.getByRole('button', { name: `Eliminar ${CLAVE}` }).click();
  const dialogo = page.locator('dialog.md-dialog');
  await expect(dialogo).toBeVisible();
  await page.waitForTimeout(1000);
  await dialogo.locator('button[value="aceptar"]').click();
  await avisoDice(`Producto ${CLAVE} eliminado`);
  await expect(page.locator('#tablaProductos')).not.toContainText(CLAVE);
});
