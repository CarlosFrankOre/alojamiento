<?php
include 'db_config.php';

include 'header.php';

// Redirigir si no ha iniciado sesión o no es un usuario
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Consulta para obtener los alojamientos seleccionados por el usuario (JOIN)
$sql = "SELECT p.accommodation_id, p.name, p.price, up.selection_id 
        FROM user_accommodations up
        JOIN accommodations p ON up.accommodation_id = p.accommodation_id
        WHERE up.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5">
    <h2 class="mb-4">Hola, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>
    <p><a href="index.php" class="btn btn-info">Ver Todos los Alojamientos</a></p>

    <h3>Tus Alojamientos Seleccionados</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                        <td>
                            <a href="delete_selection.php?selection_id=<?php echo $row['selection_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar esta selección?');">
                                Eliminar 🗑️
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Aún no has seleccionado ningún alojamiento.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include 'footer.php'; 

$conn->close();
?>