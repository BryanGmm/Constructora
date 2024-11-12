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
    $sql = "SELECT * FROM Maquinaria";
    $result = $conn->query($sql);

    $maquinaria = [];
    while ($row = $result->fetch_assoc()) {
        $maquinaria[] = $row;
    }

    echo json_encode($maquinaria);
}

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar que todos los campos necesarios estén presentes en $data
    if (
        isset($data['nombre'], $data['descripcion'], $data['tipo'], $data['estado'], 
              $data['fecha_adquisicion'], $data['costo'])
    ) {
        $stmt = $conn->prepare("INSERT INTO Maquinaria (nombre, descripcion, tipo, estado, fecha_adquisicion, costo) VALUES (?, ?, ?, ?, ?, ?)");
        // Usar "d" para valores decimales como costo
        $stmt->bind_param("sssssd", $data['nombre'], $data['descripcion'], $data['tipo'], $data['estado'], $data['fecha_adquisicion'], $data['costo']);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Maquinaria creada exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al crear maquinaria."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos"]);
    }
}

if ($method == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificamos si todos los datos necesarios están presentes
    if (
        isset($data['nombre'], $data['descripcion'], $data['tipo'], $data['estado'], 
              $data['fecha_adquisicion'], $data['costo'], $data['maquinaria_id'])
    ) {
        // Preparamos la consulta SQL para actualizar la maquinaria
        $stmt = $conn->prepare("UPDATE Maquinaria SET nombre=?, descripcion=?, tipo=?, estado=?, fecha_adquisicion=?, costo=? WHERE maquinaria_id=?");

        // Vinculamos los parámetros a la consulta preparada
        $stmt->bind_param(
            "ssssddi", 
            $data['nombre'], 
            $data['descripcion'], 
            $data['tipo'], 
            $data['estado'], 
            $data['fecha_adquisicion'], 
            $data['costo'], 
            $data['maquinaria_id']
        );

        // Ejecutamos la consulta y verificamos si tuvo éxito
        if ($stmt->execute()) {
            echo json_encode(["message" => "Maquinaria actualizada exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al actualizar maquinaria."]);
        }

        // Cerramos la declaración
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para actualizar maquinaria"]);
    }
}




if ($method == 'DELETE') {
    $input = json_decode(file_get_contents("php://input"), true);
    $maquinaria_id = $input['maquinaria_id'] ?? null;

    if ($maquinaria_id) {
        $stmt = $conn->prepare("DELETE FROM Maquinaria WHERE maquinaria_id = ?");
        $stmt->bind_param("i", $maquinaria_id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Maquinaria eliminada correctamente"]);
        } else {
            echo json_encode(["error" => "Error al eliminar maquinaria"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "ID de maquinaria no proporcionado"]);
    }
}

$conn->close();
