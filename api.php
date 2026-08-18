<?php
header('Content-Type: application/json');

$file = 'data_finances.json';

// Manejar solicitud GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // Si el archivo está vacío o no es JSON válido, devolvemos estructura por defecto
        if (empty($content) || json_decode($content) === null) {
            echo json_encode(['ingresos' => [], 'gastos' => []]);
        } else {
            echo $content;
        }
    } else {
        echo json_encode(['ingresos' => [], 'gastos' => []]);
    }
    exit;
}

// Manejar solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Validar que sea un JSON válido y que tenga la estructura esperada
    if ($data === null) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos JSON inválidos']);
        exit;
    }

    // Asegurar que tenga las claves ingresos y gastos
    if (!isset($data['ingresos']) || !isset($data['gastos'])) {
        http_response_code(400);
        echo json_encode(['error' => 'La estructura debe contener "ingresos" y "gastos"']);
        exit;
    }

    // Guardar en el archivo
    $json = json_encode($data, JSON_PRETTY_PRINT);
    if (file_put_contents($file, $json) !== false) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'No se pudo escribir el archivo. Verifica permisos.']);
    }
    exit;
}

// Otros métodos (PUT, DELETE, etc.) no permitidos
http_response_code(405);
echo json_encode(['error' => 'Método no permitido']);
?>
