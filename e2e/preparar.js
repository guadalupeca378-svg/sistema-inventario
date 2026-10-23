// Borra el inventario de demostración para que cada corrida empiece con los datos de ejemplo.
const fs = require('fs');

const archivo = process.argv[2];
for (const ruta of [archivo, `${archivo}.lock`]) {
  fs.rmSync(ruta, { force: true });
}
