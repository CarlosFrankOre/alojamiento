<?php
include 'db_config.php'; 

// 1. Verificar sesión y rol
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header("location: login.php");
    exit;
}

// 2. Verificar que se haya pasado un accommodation_id válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ID de alojamiento no válido.";
    header("location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$accommodation_id = (int)$_GET['id'];
$message_type = 'success';

// 3. Insertar la selección en la tabla user_accommodations
$sql = "INSERT INTO user_accommodations (user_id, accommodation_id) VALUES (?, ?)";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $user_id, $accommodation_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "¡Alojamiento agregado a tu cuenta con éxito!";
        $message_type = 'success'; // Mensaje de éxito
    } else {
        // ESTA PARTE EVITA EL DUPLICADO Y MANDA UN MENSAJE DE ADVERTENCIA
        if ($conn->errno == 1062) {
            $_SESSION['message'] = "Ya tienes este alojamiento seleccionado en tu cuenta. No se agregó dos veces.";
            $message_type = 'warning'; // Mensaje de advertencia
        } else {
            $_SESSION['message'] = "Error al intentar agregar el alojamiento: " . $conn->error;
            $message_type = 'danger';
        }
    }
    $stmt->close();
} else {
    $_SESSION['message'] = "Error de preparación de la consulta.";
    $message_type = 'danger';
}

$conn->close();

// Redirigir al usuario a su cuenta con el mensaje de estado
header("location: user_account.php?status=" . $message_type);
exit;
?>