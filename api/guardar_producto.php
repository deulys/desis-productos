<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse([
        'success' => false,
        'message' => 'Método no permitido.',
    ], 405);
}

$codigo = trim($_POST['codigo'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$bodegaId = $_POST['bodega_id'] ?? '';
$sucursalId = $_POST['sucursal_id'] ?? '';
$monedaId = $_POST['moneda_id'] ?? '';
$precio = trim($_POST['precio'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$materiales = $_POST['materiales'] ?? [];

// Se repiten las validaciones en backend porque el navegador no es una fuente confiable.
if ($codigo === '') jsonResponse(['success' => false, 'message' => 'El código del producto no puede estar en blanco.'], 422);
if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$/', $codigo)) jsonResponse(['success' => false, 'message' => 'El código del producto debe contener letras y números'], 422);
if (strlen($codigo) < 5 || strlen($codigo) > 15) jsonResponse(['success' => false, 'message' => 'El código del producto debe tener entre 5 y 15 caracteres.'], 422);

if ($nombre === '') jsonResponse(['success' => false, 'message' => 'El nombre del producto no puede estar en blanco.'], 422);
if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 50) jsonResponse(['success' => false, 'message' => 'El nombre del producto debe tener entre 2 y 50 caracteres.'], 422);

if (!validateIntegerId($bodegaId)) jsonResponse(['success' => false, 'message' => 'Debe seleccionar una bodega.'], 422);
if (!validateIntegerId($sucursalId)) jsonResponse(['success' => false, 'message' => 'Debe seleccionar una sucursal para la bodega seleccionada.'], 422);
if (!validateIntegerId($monedaId)) jsonResponse(['success' => false, 'message' => 'Debe seleccionar una moneda para el producto.'], 422);

if ($precio === '') jsonResponse(['success' => false, 'message' => 'El precio del producto no puede estar en blanco.'], 422);
if (!preg_match('/^(?!0+(?:\.0{1,2})?$)\d+(?:\.\d{1,2})?$/', $precio)) jsonResponse(['success' => false, 'message' => 'El precio del producto debe ser un número positivo con hasta dos decimales.'], 422);

if (!is_array($materiales) || count($materiales) < 2) jsonResponse(['success' => false, 'message' => 'Debe seleccionar al menos dos materiales para el producto.'], 422);

if ($descripcion === '') jsonResponse(['success' => false, 'message' => 'La descripción del producto no puede estar en blanco.'], 422);
if (mb_strlen($descripcion) < 10 || mb_strlen($descripcion) > 1000) jsonResponse(['success' => false, 'message' => 'La descripción del producto debe tener entre 10 y 1000 caracteres.'], 422);

try {
    $pdo = getConnection();
    $pdo->beginTransaction();

    // El código debe ser único antes de insertar.
    $existsStmt = $pdo->prepare('SELECT COUNT(*) FROM productos WHERE codigo = :codigo');
    $existsStmt->execute(['codigo' => $codigo]);

    if ((int) $existsStmt->fetchColumn() > 0) {
        $pdo->rollBack();
        jsonResponse(['success' => false, 'message' => 'El código del producto ya está registrado.'], 409);
    }

    // Evita guardar una sucursal que no corresponda a la bodega elegida.
    $relationStmt = $pdo->prepare('SELECT COUNT(*) FROM sucursales WHERE id = :sucursal_id AND bodega_id = :bodega_id AND activo = true');
    $relationStmt->execute([
        'sucursal_id' => (int) $sucursalId,
        'bodega_id' => (int) $bodegaId,
    ]);

    if ((int) $relationStmt->fetchColumn() === 0) {
        $pdo->rollBack();
        jsonResponse(['success' => false, 'message' => 'La sucursal seleccionada no pertenece a la bodega indicada.'], 422);
    }

    $insertStmt = $pdo->prepare(
        'INSERT INTO productos (codigo, nombre, bodega_id, sucursal_id, moneda_id, precio, descripcion)
         VALUES (:codigo, :nombre, :bodega_id, :sucursal_id, :moneda_id, :precio, :descripcion)
         RETURNING id'
    );

    $insertStmt->execute([
        'codigo' => $codigo,
        'nombre' => $nombre,
        'bodega_id' => (int) $bodegaId,
        'sucursal_id' => (int) $sucursalId,
        'moneda_id' => (int) $monedaId,
        'precio' => $precio,
        'descripcion' => $descripcion,
    ]);

    $productId = (int) $insertStmt->fetchColumn();
    $materialStmt = $pdo->prepare('INSERT INTO producto_materiales (producto_id, material) VALUES (:producto_id, :material)');

    // Los materiales se guardan aparte para mantener la relación uno a muchos.
    foreach (array_unique($materiales) as $material) {
        $material = trim((string) $material);
        if ($material === '') continue;
        $materialStmt->execute([
            'producto_id' => $productId,
            'material' => $material,
        ]);
    }

    $pdo->commit();

    jsonResponse([
        'success' => true,
        'message' => 'Producto guardado correctamente.',
        'data' => ['id' => $productId],
    ], 201);
} catch (Throwable $exception) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    if ($exception instanceof PDOException && $exception->getCode() === '23505') {
        jsonResponse(['success' => false, 'message' => 'El código del producto ya está registrado.'], 409);
    }

    jsonResponse([
        'success' => false,
        'message' => 'Error al guardar el producto.',
    ], 500);
}
