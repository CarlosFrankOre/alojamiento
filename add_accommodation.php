<?php
include 'db_config.php';

// Redirigir si no ha iniciado sesión como administrador o si no es una solicitud POST
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: login.php");
    exit;
}

// Directorio donde se guardarán las imágenes
define('UPLOAD_DIR', 'images/');

// 1. Obtener y sanitizar datos del formulario
$name = $conn->real_escape_string($_POST['name'] ?? '');
$description = $conn->real_escape_string($_POST['description'] ?? '');
$price = floatval($_POST['price'] ?? 0);
$image_url = ''; 

// 2. Manejo de la subida del archivo y copia al servidor
if (isset($_FILES["accommodation_image"]) && $_FILES["accommodation_image"]["error"] == 0) {
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES["accommodation_image"]["tmp_name"]);

    if (!in_array($file_type, $allowed_types)) {
        $_SESSION['message'] = "ERROR: Tipo de archivo no permitido. Solo JPG, PNG y GIF.";
        header("location: admin_panel.php");
        exit;
    }
    
    // Generar un nombre de archivo único para evitar colisiones
    $file_extension = pathinfo($_FILES["accommodation_image"]["name"], PATHINFO_EXTENSION);
    // Usamos uniqid() para garantizar que el nombre de archivo es único
    $new_file_name = uniqid() . "." . $file_extension;
    $target_file = UPLOAD_DIR . $new_file_name;
    
    // Intentar mover el archivo subido del temporal a la carpeta final (images/)
    if (move_uploaded_file($_FILES["accommodation_image"]["tmp_name"], $target_file)) {
        // La subida fue exitosa, guardamos SOLAMENTE el nombre del archivo para la DB
        $image_url = $new_file_name;
    } else {
        $_SESSION['message'] = "ERROR: Hubo un error al copiar el archivo al directorio del servidor. Verifique permisos.";
        header("location: admin_panel.php");
        exit;
    }
} else {
    $_SESSION['message'] = "ERROR: No se seleccionó ningún archivo o hubo un error de subida.";
    header("location: admin_panel.php");
    exit;
}

// 3. INSERT: Agregar el nuevo alojamiento a la base de datos
// Se inserta el nombre del archivo (contenida en $image_url), no la ruta completa.
$sql = "INSERT INTO accommodations (name, description, price, image_url) VALUES (?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ssds", $name, $description, $price, $image_url);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Alojamiento agregado con éxito y la imagen fue guardada como: " . htmlspecialchars($image_url);
    } else {
        // Si falla la inserción en la DB, eliminar el archivo que ya se había subido para limpiar
        if (!empty($image_url) && file_exists(UPLOAD_DIR . $image_url)) {
            unlink(UPLOAD_DIR . $image_url);
        }
        $_SESSION['message'] = "ERROR: No se pudo agregar el alojamiento a la DB. " . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['message'] = "ERROR: Error al preparar la sentencia SQL.";
}

$conn->close();
header("location: admin_panel.php");
exit;
?>