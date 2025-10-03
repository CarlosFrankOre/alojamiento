<?php
include 'db_config.php';

// 1. Verificación de Seguridad: Redirigir si no es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    // Si no está logueado o no es 'admin', lo envía al login
    header("location: login.php");
    exit;
}

// 2. Manejar mensajes de sesión
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    // Limpiar el mensaje después de mostrarlo
    unset($_SESSION['message']);
}

include 'header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4 text-primary">Panel de Administrador 🔑</h2>
            <p class="lead">Bienvenido, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>. Tu única función
                es agregar nuevos alojamientos.</p>

            <hr>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">➕ Agregar Nuevo Alojamiento</h4>
                </div>
                <div class="card-body">

                    <?php
                    // 3. Mostrar mensajes de estado (éxito o error)
                    if (!empty($message)) {
                        $alert_class = strpos($message, 'ERROR') !== false ? 'alert-danger' : 'alert-success';
                        echo '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">';
                        echo htmlspecialchars($message);
                        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                        echo '</div>';
                    }
                    ?>

                    <form action="add_accommodation.php" method="POST" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del Alojamiento</label>
                            <input type="text" class="form-control" id="name" name="name" required maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Precio por Noche ($)</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0.01"
                                required>
                        </div>

                        <div class="mb-4">
                            <label for="accommodation_image" class="form-label">Subir Imagen del Alojamiento</label>
                            <input type="file" class="form-control" id="accommodation_image" name="accommodation_image"
                                accept="image/*" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Agregar Alojamiento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
$conn->close();
?>