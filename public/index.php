<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Sistema de Control de Inventario</title>
</head>
<body>
    <h1>Sistema de Control de Inventario</h1>
    <p>Estado del servicio: <strong>OK</strong></p>
    <p>Versión: <?= htmlspecialchars(getenv('APP_VERSION') ?: '1.0.0') ?></p>
</body>
</html>
