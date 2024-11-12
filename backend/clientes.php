<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Constructora";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida a la base de datos."]));
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    $sql = "SELECT * FROM clientes";
    $result = $conn->query($sql);

    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[] = $row;
    }

    echo json_encode($clientes);
}

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['nombre'], $data['direccion'], $data['email'])) {
        $stmt = $conn->prepare("INSERT INTO clientes (nombre, direccion, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $data['nombre'], $data['direccion'], $data['email']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Cliente creado exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al crear cliente."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para crear cliente"]);
    }
}

if ($method == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['cliente_id'], $data['nombre'], $data['direccion'], $data['email'])) {
        $stmt = $conn->prepare("UPDATE clientes SET nombre=?, direccion=?, email=? WHERE cliente_id=?");
        $stmt->bind_param("sssi", $data['nombre'], $data['direccion'], $data['email'], $data['cliente_id']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Cliente actualizado exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al actualizar cliente."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para actualizar cliente"]);
    }
}

if ($method == 'DELETE') {
    $input = json_decode(file_get_contents("php://input"), true);
    $cliente_id = $input['cliente_id'] ?? null;

    if ($cliente_id) {
        $stmt = $conn->prepare("DELETE FROM clientes WHERE cliente_id = ?");
        $stmt->bind_param("i", $cliente_id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Cliente eliminado correctamente"]);
        } else {
            echo json_encode(["error" => "Error al eliminar cliente"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "ID de cliente no proporcionado"]);
    }
}

$conn->close();
?>
