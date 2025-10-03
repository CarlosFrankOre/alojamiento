<?php
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user' || !isset($_GET['selection_id'])) {
    header("location: login.php");
    exit;
}

$selection_id = $_GET['selection_id'];
$user_id = $_SESSION['user_id'];

// DELETE: Eliminar la selección de la tabla user_accommodations
$sql = "DELETE FROM user_accommodations WHERE selection_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $selection_id, $user_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Selección eliminada con éxito.";
} else {
    $_SESSION['message'] = "Error al intentar eliminar la selección.";
}

$stmt->close();
$conn->close();
header("location: user_account.php");
exit;
?>