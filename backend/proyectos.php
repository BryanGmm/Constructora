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
    $sql = "SELECT * FROM Proyectos";
    $result = $conn->query($sql);

    $proyectos = [];
    while ($row = $result->fetch_assoc()) {
        if ($row['imagen']) {
            $row['imagen'] = "data:image/jpeg;base64," . base64_encode($row['imagen']);
        }
        $proyectos[] = $row;
    }

    echo json_encode($proyectos);
}

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $imagen = isset($data['imagen']) ? base64_decode($data['imagen']) : null;

    $stmt = $conn->prepare("INSERT INTO Proyectos (nombre, descripcion, ubicacion, fecha_inicio, fecha_fin, fecha_reprogramada, estado, porcentaje_avance, inversion_inicial, inversion_final, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssd", $data['nombre'], $data['descripcion'], $data['ubicacion'], $data['fecha_inicio'], $data['fecha_fin'], $data['fecha_reprogramada'], $data['estado'], $data['porcentaje_avance'], $data['inversion_inicial'], $data['inversion_final'], $imagen);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Proyecto creado exitosamente."]);
    } else {
        echo json_encode(["error" => "Error al crear proyecto."]);
    }
    $stmt->close();
}

if ($method == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificamos si la imagen está presente en los datos recibidos
    $imagen = isset($data['imagen']) ? base64_decode($data['imagen']) : null;

    // Construimos la consulta SQL condicionalmente
    if ($imagen !== null) {
        $stmt = $conn->prepare("UPDATE Proyectos SET nombre=?, descripcion=?, ubicacion=?, fecha_inicio=?, fecha_fin=?, fecha_reprogramada=?, estado=?, porcentaje_avance=?, inversion_inicial=?, inversion_final=?, imagen=? WHERE proyecto_id=?");
        $stmt->bind_param("sssssssssssi", $data['nombre'], $data['descripcion'], $data['ubicacion'], $data['fecha_inicio'], $data['fecha_fin'], $data['fecha_reprogramada'], $data['estado'], $data['porcentaje_avance'], $data['inversion_inicial'], $data['inversion_final'], $imagen, $data['proyecto_id']);
    } else {
        // Si no hay imagen, omitimos el campo imagen en la actualización
        $stmt = $conn->prepare("UPDATE Proyectos SET nombre=?, descripcion=?, ubicacion=?, fecha_inicio=?, fecha_fin=?, fecha_reprogramada=?, estado=?, porcentaje_avance=?, inversion_inicial=?, inversion_final=? WHERE proyecto_id=?");
        $stmt->bind_param("ssssssssssi", $data['nombre'], $data['descripcion'], $data['ubicacion'], $data['fecha_inicio'], $data['fecha_fin'], $data['fecha_reprogramada'], $data['estado'], $data['porcentaje_avance'], $data['inversion_inicial'], $data['inversion_final'], $data['proyecto_id']);
    }

    if ($stmt->execute()) {
        echo json_encode(["message" => "Proyecto actualizado exitosamente."]);
    } else {
        echo json_encode(["error" => "Error al actualizar proyecto."]);
    }
    $stmt->close();
}

if ($method == 'DELETE') {
    $input = json_decode(file_get_contents("php://input"), true);
    $proyecto_id = $input['proyecto_id'] ?? null;

    if ($proyecto_id) {
        $stmt = $conn->prepare("DELETE FROM Proyectos WHERE proyecto_id = ?");
        $stmt->bind_param("i", $proyecto_id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Proyecto eliminado correctamente"]);
        } else {
            echo json_encode(["error" => "Error al eliminar proyecto"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "ID de proyecto no proporcionado"]);
    }
}

$conn->close();
?>
