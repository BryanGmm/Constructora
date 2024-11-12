<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "constructora";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida a la base de datos."]));
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    $sql = "SELECT u.usuario_id, u.username, u.nombre, u.email, r.nombre AS rol 
            FROM Usuarios u 
            LEFT JOIN Roles r ON u.rol_id = r.rol_id";
    $result = $conn->query($sql);

    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }

    echo json_encode($usuarios);
}
if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['username']) || empty($data['nombre']) || empty($data['email']) || empty($data['password']) || empty($data['rol_id'])) {
        echo json_encode(["error" => "Datos incompletos o incorrectos."]);
        exit;
    }

    // Verificar si el nombre de usuario ya existe
    $stmt = $conn->prepare("SELECT usuario_id FROM Usuarios WHERE username = ?");
    $stmt->bind_param("s", $data['username']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // El nombre de usuario ya existe
        echo json_encode(["error" => "El nombre de usuario ya está en uso."]);
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Proceder con la inserción si el nombre de usuario no existe
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO Usuarios (username, nombre, email, password_hash, rol_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $data['username'], $data['nombre'], $data['email'], $hashed_password, $data['rol_id']);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Usuario creado exitosamente."]);
    } else {
        echo json_encode(["error" => "Error al crear usuario: " . $stmt->error]);
    }

    $stmt->close();
}

if ($method == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['usuario_id']) || empty($data['nombre']) || empty($data['email']) || empty($data['username']) || empty($data['rol_id'])) {
        echo json_encode(["error" => "Datos incompletos para actualizar."]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE Usuarios SET nombre = ?, email = ?, username = ?, rol_id = ? WHERE usuario_id = ?");
    $stmt->bind_param("sssii", $data['nombre'], $data['email'], $data['username'], $data['rol_id'], $data['usuario_id']);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Usuario actualizado exitosamente."]);
    } else {
        echo json_encode(["error" => "Error al actualizar usuario: " . $stmt->error]);
    }

    $stmt->close();
}

if ($method == 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $usuario_id = $input['usuario_id'] ?? null;

    if ($usuario_id) {
        $stmt = $conn->prepare("DELETE FROM Usuarios WHERE usuario_id = ?");
        $stmt->bind_param("i", $usuario_id);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Usuario eliminado correctamente']);
        } else {
            echo json_encode(['error' => 'Error al eliminar usuario: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['error' => 'ID de usuario no proporcionado']);
    }

    $stmt->close();
}

$conn->close();
?>
