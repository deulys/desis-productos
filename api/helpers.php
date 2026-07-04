<?php
function jsonResponse(array $payload, int $statusCode = 200): void
{
    // Respuesta estándar para que todos los endpoints hablen el mismo formato JSON.
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function validateIntegerId(mixed $value): bool
{
    // Valida IDs recibidos desde formularios o query string.
    return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
}
