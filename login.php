<?php
include 'db_config.php'; 

// Variables para manejar errores y datos
$username = $password = '';
$username_err = $password_err = $login_err = '';
$success_message = '';

// Verificar si existe un mensaje de éxito (ej. después del registro)
if (isset($_GET['message']) && $_GET['message'] === 'success_register') {
    $success_message = "✅ ¡Registro exitoso! Ya puedes iniciar sesión con tu nueva cuenta.";
}

// Procesa el formulario de inicio de sesión cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Obtener y Sanitizar datos
    if (empty(trim($_POST["username"]))) {
        $username_err = "Por favor, ingresa el nombre de usuario.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, ingresa tu contraseña.";
    } else {
        $password = $_POST["password"];
    }

    // 2. Verificar credenciales si no hay errores de validación en los inputs
    if (empty($username_err) && empty($password_err)) {
        
        $sql = "SELECT user_id, username, password_hash, user_role FROM users WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();
                    
                    // 3. Verificar la contraseña hasheada
                    if (password_verify($password, $user['password_hash'])) {
                        // Contraseña correcta, iniciar sesión
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['user_role'] = $user['user_role'];

                        // 4. Redirigir según el rol
                        if ($user['user_role'] === 'admin') {
                            header("location: admin_panel.php");
                        } else {
                            header("location: user_account.php");
                        }
                        exit;
                    } else {
                        $login_err = "Contraseña incorrecta.";
                    }
                } else {
                    $login_err = "No se encontró la cuenta con ese nombre de usuario.";
                }
            } else {
                $login_err = "Error en la consulta. Inténtalo de nuevo más tarde.";
            }
            $stmt->close();
        }
    }
}

// Incluye el encabezado y el HTML de Bootstrap
include 'header.php'; 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Iniciar Sesión</h3>
                </div>
                <div class="card-body">
                    
                    <?php 
                    // Muestra mensaje de registro exitoso
                    if (!empty($success_message)) {
                        echo '<div class="alert alert-success">' . $success_message . '</div>';
                    }
                    
                    // Muestra el error de inicio de sesión
                    if (!empty($login_err)) {
                        echo '<div class="alert alert-danger">' . $login_err . '</div>';
                    }
                    ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de Usuario</label>
                            <input type="text" name="username" class="form-control <?php echo (!empty($username_err) || !empty($login_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>" required>
                            <span class="invalid-feedback"><?php echo $username_err; ?></span>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control <?php echo (!empty($password_err) || !empty($login_err)) ? 'is-invalid' : ''; ?>" required>
                            <span class="invalid-feedback"><?php echo $password_err; ?></span>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Acceder</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    ¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$conn->close();
include 'footer.php';
?>