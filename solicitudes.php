<?php
session_start();
include("conexion.php");

// Consulta para obtener libros, estados de solicitudes y datos del usuario solicitante
$Query = "SELECT libro.*, solicitudes.estado, solicitudes.usuario_sol AS solicitante, usuarios.telefono AS telefono 
          FROM libro 
          INNER JOIN solicitudes 
          ON libro.nombre = solicitudes.libro 
          INNER JOIN usuarios 
          ON solicitudes.usuario_sol = usuarios.user
          WHERE solicitudes.usuario_pres = :username";
$Result = $conn->prepare($Query);
$Result->execute([':username' => $_SESSION["username"]]);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/solicitudes.css">
</head>

<body>
    <header>
        <h1>
            <?php echo htmlspecialchars($_SESSION["username"]); ?> 👋
        </h1>
        <nav>
            <ul>
                <li><a href="home.php">Inicio</a></li>
                <li><a href="solicitudes.php" style="color: #ff5252;">Solicitudes</a></li>
                <li><a href="guardados.php">Guardados</a></li>
                <li><a href="mibiblioteca.php">Biblioteca</a></li>
            </ul>
        </nav>
    </header>

    <h1>Tus Solicitudes</h1>

    <!-- Botones de filtro -->
    <div class="filter-buttons">
        <button onclick="filterByStatus('todos')">Ver Todos</button>
        <button onclick="filterByStatus('aceptados')">Aceptados</button>
        <button onclick="filterByStatus('rechazados')">Rechazados</button>
        <button onclick="filterByStatus('en espera')">En espera</button>
    </div>

    <!-- Contenedor de libros -->
    <div class="carousel-container">
        <button class="arrow left" onclick="moveCarousel(-1)">&#10094;</button>
        <div class="carousel">
            <?php
            while ($row = $Result->fetch(PDO::FETCH_ASSOC)) {
            ?>
                <div class="card-container" data-estado="<?php echo strtolower($row['estado']); ?>">
                    <div class="card-image" style="background-image: url('<?php echo htmlspecialchars($row['img']); ?>');"></div>
                    <div class="card-content">
                        <h3 class="card-title"><?php echo $row["nombre"]; ?></h3>
                        <div class="card-author"><?php echo $row["autor"]; ?></div>
                        <div class="card-status">Estado: <?php echo ucfirst($row["estado"]); ?></div>

                        <?php if (strtolower($row['estado']) === 'en espera') { ?>
                            <button onclick="acceptRequest('<?php echo $row['nombre']; ?>', '<?php echo $row['solicitante']; ?>', '<?php echo $row['telefono']; ?>')">Aceptar</button>
                            <button onclick="rejectRequest('<?php echo $row['nombre']; ?>')">Rechazar</button>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <button class="arrow right" onclick="moveCarousel(1)">&#10095;</button>
    </div>

    <script>
        function filterByStatus(status) {
            const cards = document.querySelectorAll('.card-container');
            cards.forEach(card => {
                if (status === 'todos' || card.dataset.estado === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function acceptRequest(libro, usuario, telefono) {
            const confirmMsg = `Comunícate con el usuario ${usuario}. Teléfono: ${telefono}`;
            const userAction = confirm(confirmMsg);
            if (userAction) {
                // Cambiar estado a "Aceptado" en la base de datos
                fetch(`update_request.php?action=accept&libro=${encodeURIComponent(libro)}`)
                    .then(response => response.text())
                    .then(data => {
                        alert('Solicitud aceptada.');
                        location.reload(); // Recargar la página
                    });
            }
        }

        function rejectRequest(libro) {
            if (confirm('¿Estás seguro de rechazar esta solicitud?')) {
                // Cambiar estado a "Rechazado" en la base de datos
                fetch(`update_request.php?action=reject&libro=${encodeURIComponent(libro)}`)
                    .then(response => response.text())
                    .then(data => {
                        alert('Solicitud rechazada.');
                        location.reload(); // Recargar la página
                    });
            }
        }

        function moveCarousel(direction) {
            const carousel = document.querySelector('.carousel');
            const scrollAmount = 300;
            carousel.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
        }
    </script>
</body>

</html>
