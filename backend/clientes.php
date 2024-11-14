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
        // Obtener teléfonos asociados al cliente
        $cliente_id = $row['cliente_id'];
        $telefonos_result = $conn->query("SELECT numero_telefono FROM telefonos_clientes WHERE cliente_id = $cliente_id");
        
        $telefonos = [];
        while ($telefono_row = $telefonos_result->fetch_assoc()) {
            $telefonos[] = $telefono_row['numero_telefono'];
        }
        
        $row['telefonos'] = $telefonos; // Añadir los teléfonos al cliente
        $clientes[] = $row;
    }

    echo json_encode($clientes);
}

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['nombre'], $data['direccion'], $data['email'], $data['telefono'])) {
        // Iniciar una transacción
        $conn->begin_transaction();

        try {
            // Insertar en la tabla de clientes
            $stmt = $conn->prepare("INSERT INTO clientes (nombre, direccion, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $data['nombre'], $data['direccion'], $data['email']);
            $stmt->execute();
            $cliente_id = $stmt->insert_id; // Obtener el ID del cliente recién creado
            $stmt->close();

            // Insertar el primer número de teléfono en la tabla `telefonos_clientes`
            $stmt = $conn->prepare("INSERT INTO telefonos_clientes (cliente_id, numero_telefono) VALUES (?, ?)");
            $stmt->bind_param("is", $cliente_id, $data['telefono']);
            $stmt->execute();
            $stmt->close();

            // Confirmar la transacción
            $conn->commit();
            echo json_encode(["message" => "Cliente y teléfono creados exitosamente."]);
        } catch (Exception $e) {
            // En caso de error, revertir la transacción
            $conn->rollback();
            echo json_encode(["error" => "Error al crear cliente o teléfono."]);
        }
    } else {
        echo json_encode(["error" => "Datos incompletos para crear cliente"]);
    }
}

if ($method == 'POST' && isset($_GET['action']) && $_GET['action'] == 'add_phone') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['cliente_id'], $data['numero_telefono'])) {
        $stmt = $conn->prepare("INSERT INTO telefonos_clientes (cliente_id, numero_telefono) VALUES (?, ?)");
        $stmt->bind_param("is", $data['cliente_id'], $data['numero_telefono']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Número de teléfono agregado exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al agregar número de teléfono."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para agregar teléfono"]);
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
        // Iniciar transacción
        $conn->begin_transaction();
        try {
            // Primero, eliminar los teléfonos asociados al cliente
            $stmt = $conn->prepare("DELETE FROM telefonos_clientes WHERE cliente_id = ?");
            $stmt->bind_param("i", $cliente_id);
            $stmt->execute();
            $stmt->close();

            // Luego, eliminar el cliente
            $stmt = $conn->prepare("DELETE FROM clientes WHERE cliente_id = ?");
            $stmt->bind_param("i", $cliente_id);
            $stmt->execute();
            $stmt->close();

            // Confirmar transacción
            $conn->commit();
            echo json_encode(["message" => "Cliente y teléfonos asociados eliminados correctamente"]);
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $conn->rollback();
            echo json_encode(["error" => "Error al eliminar cliente o teléfonos asociados"]);
        }
    } else {
        echo json_encode(["error" => "ID de cliente no proporcionado"]);
    }
}

$conn->close();
?>
