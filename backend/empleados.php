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
    $sql = "SELECT * FROM empleados";
    $result = $conn->query($sql);

    $empleados = [];
    while ($row = $result->fetch_assoc()) {
        $empleados[] = $row;
    }

    echo json_encode($empleados);
}

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (
        isset($data['nombre'], $data['puesto_id'], $data['salario'], $data['email'])
    ) {
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

if ($method == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (
        isset($data['empleado_id'], $data['nombre'], $data['puesto_id'], $data['salario'], $data['email'])
    ) {
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
        $stmt = $conn->prepare("DELETE FROM empleados WHERE empleado_id = ?");
        $stmt->bind_param("i", $empleado_id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Empleado eliminado correctamente"]);
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
