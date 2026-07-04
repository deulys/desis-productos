<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

try {
    $pdo = getConnection();

    // Datos base para llenar los selects de la pantalla principal.
    $bodegas = $pdo->query('SELECT id, nombre FROM bodegas WHERE activo = true ORDER BY nombre')->fetchAll();
    $monedas = $pdo->query('SELECT id, nombre FROM monedas WHERE activo = true ORDER BY nombre')->fetchAll();

    jsonResponse([
        'success' => true,
        'data' => [
            'bodegas' => $bodegas,
            'monedas' => $monedas,
        ],
    ]);
} catch (Throwable $exception) {
    jsonResponse([
        'success' => false,
        'message' => 'Error al cargar los catálogos.',
    ], 500);
}
