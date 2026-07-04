<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

$bodegaId = $_GET['bodega_id'] ?? null;

if (!validateIntegerId($bodegaId)) {
    jsonResponse([
        'success' => false,
        'message' => 'Debe seleccionar una bodega válida.',
    ], 422);
}

try {
    $pdo = getConnection();
    // Solo se listan las sucursales activas de la bodega seleccionada.
    $stmt = $pdo->prepare('SELECT id, nombre FROM sucursales WHERE bodega_id = :bodega_id AND activo = true ORDER BY nombre');
    $stmt->execute(['bodega_id' => (int) $bodegaId]);

    jsonResponse([
        'success' => true,
        'data' => $stmt->fetchAll(),
    ]);
} catch (Throwable $exception) {
    jsonResponse([
        'success' => false,
        'message' => 'Error al cargar las sucursales.',
    ], 500);
}
