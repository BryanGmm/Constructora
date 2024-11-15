<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
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

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['proyecto_id'])) {
    $proyecto_id = $_GET['proyecto_id'];

    $stmt = $conn->prepare("SELECT * FROM tareas_proyecto WHERE proyecto_id = ?");
    $stmt->bind_param("i", $proyecto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tareas = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($tareas);
    $stmt->close();
} elseif ($method === 'POST') {
    // Actualizar una tarea específica
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['tarea_id'], $data['nombre'], $data['descripcion'], $data['completada'])) {
        $tarea_id = $data['tarea_id'];
        $nombre = $data['nombre'];
        $descripcion = $data['descripcion'];
        $completada = $data['completada'] ? 1 : 0;

        $query = "UPDATE tareas_proyecto SET nombre = ?, descripcion = ?, completada = ? WHERE tarea_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssii", $nombre, $descripcion, $completada, $tarea_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Tarea actualizada correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar la tarea."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Datos incompletos para actualizar la tarea."]);
    }

} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}

$conn->close();
?>
