<?php
header('Content-Type: application/json');
// Configuración de CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "constructora";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida a la base de datos: " . $conn->connect_error]));
}

// Obtener el método de solicitud
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Obtener empleados asignados a un proyecto específico
    $proyecto_id = $_GET['proyecto_id'] ?? null;
    if ($proyecto_id) {
        $sql = "SELECT pe.proyecto_empleado_id, e.nombre, pe.fecha_asignacion, pe.rol_en_proyecto
        FROM proyectos_empleados pe
        JOIN empleados e ON pe.empleado_id = e.empleado_id
        WHERE pe.proyecto_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $proyecto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $empleados = [];
        while ($row = $result->fetch_assoc()) {
            $empleados[] = $row;
        }
        echo json_encode($empleados);
        $stmt->close();
    } else {
        echo json_encode(["error" => "ID del proyecto no proporcionado."]);
    }
} elseif ($method == 'POST') {
    // Leer y decodificar el cuerpo JSON de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar que la decodificación fue exitosa
    if (is_null($data)) {
        echo json_encode(["error" => "No se pudo decodificar el cuerpo de la solicitud."]);
        exit;
    }

    // Verifica si es una solicitud de eliminación simulada
    if (isset($data['_method']) && $data['_method'] === 'DELETE' && isset($data['proyecto_empleado_id'])) {
        $proyecto_empleado_id = $data['proyecto_empleado_id'];
    
        $stmt = $conn->prepare("DELETE FROM proyectos_empleados WHERE proyecto_empleado_id = ?");
        $stmt->bind_param("i", $proyecto_empleado_id);
    
        if ($stmt->execute()) {
            echo json_encode(["message" => "Empleado eliminado correctamente."]);
        } else {
            echo json_encode(["error" => "Error al eliminar empleado: " . $stmt->error]);
        }
        $stmt->close();
    } 
    // Manejo para asignar nuevo empleado
    elseif (isset($data['proyecto_id'], $data['empleado_id'], $data['fecha_asignacion'])) {
        $proyecto_id = $data['proyecto_id'];
        $empleado_id = $data['empleado_id'];
        $fecha_asignacion = $data['fecha_asignacion'];
        $rol_en_proyecto = $data['rol_en_proyecto'] ?? null;

        $stmt = $conn->prepare("INSERT INTO proyectos_empleados (proyecto_id, empleado_id, fecha_asignacion, rol_en_proyecto) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $proyecto_id, $empleado_id, $fecha_asignacion, $rol_en_proyecto);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Empleado asignado correctamente."]);
        } else {
            echo json_encode(["error" => "Error al asignar empleado: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Datos incompletos para asignar o eliminar empleado."]);
    }
} else {
    echo json_encode(["error" => "Método de solicitud no soportado."]);
}

// Cerrar la conexión
$conn->close();
?>
