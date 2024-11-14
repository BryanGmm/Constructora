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
$action = $_GET['action'] ?? '';

if ($method == 'GET') {
    $sql = "SELECT e.*, GROUP_CONCAT(t.numero_telefono) AS telefonos
            FROM empleados e
            LEFT JOIN telefonos_empleados t ON e.empleado_id = t.empleado_id
            GROUP BY e.empleado_id";
    $result = $conn->query($sql);

    $empleados = [];
    while ($row = $result->fetch_assoc()) {
        $empleados[] = $row;
    }

    echo json_encode($empleados);
}

if ($method == 'POST' && $action == 'add_phone') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['empleado_id'], $data['numero_telefono'])) {
        $stmt = $conn->prepare("INSERT INTO telefonos_empleados (empleado_id, numero_telefono) VALUES (?, ?)");
        $stmt->bind_param("is", $data['empleado_id'], $data['numero_telefono']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Teléfono agregado exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al agregar teléfono."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para agregar teléfono"]);
    }
}

if ($method == 'PUT' && $action == 'edit_phone') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['telefono_id'], $data['numero_telefono'])) {
        $stmt = $conn->prepare("UPDATE telefonos_empleados SET numero_telefono=? WHERE telefono_id=?");
        $stmt->bind_param("si", $data['numero_telefono'], $data['telefono_id']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Teléfono actualizado exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al actualizar teléfono."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para actualizar teléfono"]);
    }
}

if ($method == 'POST' && empty($action)) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['nombre'], $data['puesto_id'], $data['salario'], $data['email'])) {
        $stmt = $conn->prepare("INSERT INTO empleados (nombre, puesto_id, salario, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sids", $data['nombre'], $data['puesto_id'], $data['salario'], $data['email']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Empleado creado exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al crear empleado."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para crear empleado"]);
    }
}

if ($method == 'PUT' && empty($action)) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['empleado_id'], $data['nombre'], $data['puesto_id'], $data['salario'], $data['email'])) {
        $stmt = $conn->prepare("UPDATE empleados SET nombre=?, puesto_id=?, salario=?, email=? WHERE empleado_id=?");
        $stmt->bind_param("sidsi", $data['nombre'], $data['puesto_id'], $data['salario'], $data['email'], $data['empleado_id']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Empleado actualizado exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al actualizar empleado."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para actualizar empleado"]);
    }
}

if ($method == 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $empleado_id = $data['empleado_id'] ?? null;

    if ($empleado_id) {
        // Eliminar teléfonos asociados primero
        $stmt = $conn->prepare("DELETE FROM telefonos_empleados WHERE empleado_id = ?");
        $stmt->bind_param("i", $empleado_id);
        $stmt->execute();
        $stmt->close();

        // Luego eliminar el empleado
        $stmt = $conn->prepare("DELETE FROM empleados WHERE empleado_id = ?");
        $stmt->bind_param("i", $empleado_id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Empleado y teléfonos asociados eliminados correctamente"]);
        } else {
            echo json_encode(["error" => "Error al eliminar empleado"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "ID de empleado no proporcionado"]);
    }
}

$conn->close();
?>
