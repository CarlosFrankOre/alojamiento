<?php
include 'db_config.php'; 

$username = $email = $password = '';
$username_err = $email_err = $password_err = $general_err = '';

// Procesa el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Validación y Sanitización de Inputs
    
    // Validar nombre de usuario
    if (empty(trim($_POST["username"]))) {
        $username_err = "Por favor, ingresa un nombre de usuario.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validar email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor, ingresa un correo electrónico.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
         $email_err = "El formato del correo electrónico no es válido.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validar contraseña
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, ingresa una contraseña.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // 2. Comprobar si no hay errores de validación
    if (empty($username_err) && empty($email_err) && empty($password_err)) {
        
        // 3. Verificar si el usuario o email ya existen
        $sql = "SELECT user_id FROM users WHERE username = ? OR email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $param_username, $param_email);
            $param_username = $username;
            $param_email = $email;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows > 0) {
                    // Si ya existe un usuario con ese nombre o email
                    $general_err = "El nombre de usuario o el correo electrónico ya está en uso.";
                } else {
                    // 4. Insertar el nuevo usuario
                    
                    // Contraseña segura: Hashing
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $user_role = 'user'; // Rol predeterminado para nuevos registros

                    $sql_insert = "INSERT INTO users (username, password_hash, email, user_role) VALUES (?, ?, ?, ?)";
                    
                    if ($stmt_insert = $conn->prepare($sql_insert)) {
                        $stmt_insert->bind_param("ssss", $param_username, $param_hash, $param_email, $param_role);
                        $param_username = $username;
                        $param_hash = $password_hash;
                        $param_email = $email;
                        $param_role = $user_role;
                        
                        if ($stmt_insert->execute()) {
                            // Registro exitoso, redirigir a la página de inicio de sesión
                            header("location: login.php?message=success_register");
                            exit();
                        } else {
                            $general_err = "Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
                        }
                        $stmt_insert->close();
                    }
                }
            } else {
                $general_err = "Error en la consulta de verificación.";
            }
            $stmt->close();
        }
    }
    
    $conn->close();
}

include 'header.php'; 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">Crear una Cuenta</h3>
                </div>
                <div class="card-body">
                    <?php 
                    // Muestra el error general si existe
                    if (!empty($general_err)) {
                        echo '<div class="alert alert-danger">' . $general_err . '</div>';
                    }
                    ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de Usuario</label>
                            <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>" required>
                            <span class="invalid-feedback"><?php echo $username_err; ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>" required>
                            <span class="invalid-feedback"><?php echo $email_err; ?></span>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" required>
                            <span class="invalid-feedback"><?php echo $password_err; ?></span>
                            <small class="form-text text-muted">Mínimo 6 caracteres.</small>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Registrarme</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    ¿Ya tienes una cuenta? <a href="login.php">Inicia Sesión aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>