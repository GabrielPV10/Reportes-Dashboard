<?php
require_once 'conectar.php';

// Obtener los datos del formulario
$nombre_completo = $_POST['nombre_completo'];
$email = $_POST['email'];
$nombre_compania = $_POST['nombre_compania']; // Nuevo campo
$password = $_POST['password'];
$rol_id = $_POST['rol_id'];

try {
    $conn->begin_transaction();

    // --- LÓGICA PARA LA COMPAÑÍA ---
    // 1. Revisar si la compañía ya existe
    $stmt = $conn->prepare("SELECT compania_id FROM DimCompania WHERE nombre_compania = ?");
    $stmt->bind_param("s", $nombre_compania);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // 2. Si existe, obtenemos su ID
        $compania_id = $resultado->fetch_assoc()['compania_id'];
    } else {
        // 3. Si no existe, la creamos
        $stmt = $conn->prepare("INSERT INTO DimCompania (nombre_compania) VALUES (?)");
        $stmt->bind_param("s", $nombre_compania);
        $stmt->execute();
        $compania_id = $stmt->insert_id; // Obtenemos el ID de la compañía recién creada
    }

    // --- LÓGICA PARA EL USUARIO ---
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // 4. Insertar el nuevo usuario con el ID de la compañía
    $sql = "INSERT INTO Usuarios (nombre_completo, email, password_hash, rol_id, compania_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $nombre_completo, $email, $password_hash, $rol_id, $compania_id);

    if ($stmt->execute()) {
        $conn->commit();
        echo "<h1>¡Registro exitoso!</h1>";
        echo "<p>El usuario y la compañía han sido registrados correctamente.</p>";
        // RUTA CORREGIDA
        echo '<a href="index.php">Ir a Iniciar Sesión</a>';
    } else {
        throw new Exception($stmt->error);
    }

} catch (Exception $e) {
    $conn->rollback();
    echo "<h1>Error en el registro</h1>";
    echo "<p>Hubo un problema: " . $e->getMessage() . "</p>";
}

$stmt->close();
$conn->close();
?>