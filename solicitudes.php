<?php
session_start();
include("conexion.php");

// Consulta para obtener libros y estados de solicitudes
$Query = "SELECT libro.*, solicitudes.estado 
          FROM libro 
          INNER JOIN solicitudes 
          ON libro.nombre = solicitudes.libro 
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
                <a href="libro.php?nombre=<?php echo $row['nombre']; ?>" class="card-link">
                    <div class="card-container" 
                         data-estado="<?php echo strtolower($row['estado']); ?>">
                        <div class="card-image" style="background-image: url('<?php echo htmlspecialchars($row['img']); ?>');"></div>
                        <div class="card-overlay"></div>
                        <div class="card-content">
                            <h3 class="card-title"><?php echo $row["nombre"]; ?></h3>
                            <div class="card-author"><?php echo $row["autor"]; ?></div>
                            <div class="card-rating">
                                <span>⭐ <?php echo $row["pt"]; ?></span>
                            </div>
                            <div class="card-status">Estado: <?php echo ucfirst($row["estado"]); ?></div>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
        <button class="arrow right" onclick="moveCarousel(1)">&#10095;</button>
    </div>

    <script>
        function filterByStatus(status) {
    const buttons = document.querySelectorAll('.filter-buttons button');
    const cards = document.querySelectorAll('.card-container');

    // Actualizar el estado de los botones
    buttons.forEach(button => {
        button.classList.remove('active'); // Quitar la clase activa de todos los botones
        if (button.textContent.toLowerCase().includes(status)) {
            button.classList.add('active'); // Añadir la clase activa al botón actual
        }
    });

    // Filtrar las tarjetas según el estado
    cards.forEach(card => {
        if (status === 'todos' || card.dataset.estado === status) {
            card.parentElement.style.display = 'block'; // Mostrar
        } else {
            card.parentElement.style.display = 'none'; // Ocultar
        }
    });
}

        function moveCarousel(direction) {
            const carousel = document.querySelector('.carousel');
            const scrollAmount = 300; // Ajusta según el diseño
            carousel.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
        }
    </script>
</body>

</html>
