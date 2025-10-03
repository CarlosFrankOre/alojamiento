<?php
include 'db_config.php';
include 'header.php'; 

// Consulta para obtener todos los alojamientos
$sql = "SELECT * FROM accommodations";
$result = $conn->query($sql);
?>

<div class="container mt-5">
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100"> <div style="height: 200px; overflow: hidden;">
                            <img src="images/<?php echo htmlspecialchars($row['image_url']); ?>" 
                                 class="card-img-top w-100 h-100 object-fit-cover" 
                                 alt="<?php echo htmlspecialchars($row['name']); ?>">
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($row['description'], 0, 80)) . '...'; ?></p>
                            <p class="card-text mt-auto"><strong>$<?php echo htmlspecialchars($row['price']); ?> / Noche</strong></p>
                            
                            <?php 
                            // 2. Lógica para deshabilitar el botón si ya está seleccionado
                            $is_selected = false;
                            if (isset($_SESSION['user_id'])) {
                                $check_sql = "SELECT selection_id FROM user_accommodations WHERE user_id = ? AND accommodation_id = ?";
                                $check_stmt = $conn->prepare($check_sql);
                                $check_stmt->bind_param("ii", $_SESSION['user_id'], $row['accommodation_id']);
                                $check_stmt->execute();
                                $check_stmt->store_result();
                                if ($check_stmt->num_rows > 0) {
                                    $is_selected = true;
                                }
                                $check_stmt->close();
                            }
                            ?>

                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php if ($is_selected): ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Ya Seleccionado</button>
                                <?php else: ?>
                                    <a href="select_accommodation.php?id=<?php echo $row['accommodation_id']; ?>" class="btn btn-primary btn-sm">Seleccionar</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-secondary btn-sm">Inicia sesión para Seleccionar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No hay alojamientos disponibles.</p>
        <?php endif; ?>
    </div>
</div>

<?php

include 'footer.php'; 
$conn->close();
?>