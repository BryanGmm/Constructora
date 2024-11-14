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

    $stmt = $conn->prepare("INSERT INTO Proyectos (nombre, descripcion, ubicacion, fecha_inicio, fecha_fin, fecha_reprogramada, estado, inversion_inicial, inversion_final, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssd", $data['nombre'], $data['descripcion'], $data['ubicacion'], $data['fecha_inicio'], $data['fecha_fin'], $data['fecha_reprogramada'], $data['estado'], $data['inversion_inicial'], $data['inversion_final'], $imagen);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Proyecto creado exitosamente."]);
    } else {
        echo json_encode(["error" => "Error al crear proyecto."]);
    }
    $stmt->close();
}

if ($method == 'PUT' && isset($_GET['update_percentage'])) {
    $proyecto_id = $_GET['proyecto_id'] ?? null;

    if ($proyecto_id) {
        // Obtener el total de tareas y las tareas completadas para el proyecto
        $sqlTotal = "SELECT COUNT(*) as total FROM tareas_proyecto WHERE proyecto_id = ?";
        $stmtTotal = $conn->prepare($sqlTotal);
        $stmtTotal->bind_param("i", $proyecto_id);
        $stmtTotal->execute();
        $resultTotal = $stmtTotal->get_result();
        $totalTareas = $resultTotal->fetch_assoc()['total'];
        $stmtTotal->close();

        $sqlCompleted = "SELECT COUNT(*) as completed FROM tareas_proyecto WHERE proyecto_id = ? AND completada = 1";
        $stmtCompleted = $conn->prepare($sqlCompleted);
        $stmtCompleted->bind_param("i", $proyecto_id);
        $stmtCompleted->execute();
        $resultCompleted = $stmtCompleted->get_result();
        $tareasCompletadas = $resultCompleted->fetch_assoc()['completed'];
        $stmtCompleted->close();

        // Calcular el porcentaje de avance
        $porcentajeAvance = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0;

        // Actualizar el porcentaje de avance en la tabla Proyectos
        $stmtUpdate = $conn->prepare("UPDATE Proyectos SET porcentaje_avance = ? WHERE proyecto_id = ?");
        $stmtUpdate->bind_param("ii", $porcentajeAvance, $proyecto_id);
        if ($stmtUpdate->execute()) {
            echo json_encode(["message" => "Porcentaje de avance actualizado exitosamente.", "porcentaje_avance" => $porcentajeAvance]);
        } else {
            echo json_encode(["error" => "Error al actualizar el porcentaje de avance."]);
        }
        $stmtUpdate->close();
    } else {
        echo json_encode(["error" => "ID de proyecto no proporcionado"]);
    }
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
