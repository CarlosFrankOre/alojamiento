<?php
include 'db_config.php'; 

// 1. Destruir todas las variables de sesión.
$_SESSION = array();

// 2. Si se desea destruir la sesión completamente, también se debe eliminar 
// la cookie de sesión. Nota: Esto destruirá la sesión, y no solo los datos de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Finalmente, destruye la sesión.
session_destroy();

// 4. Redirige al usuario a la página de inicio (Landing Page)
header("location: index.php");
exit;
?>