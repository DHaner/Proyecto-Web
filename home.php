<?php
    session_start();
    include("conexion.php");

    // Asegúrate de que la consulta es correcta
    $Query = "SELECT * FROM libro";
    $Result = $conn->query($Query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <header>
        <h1>
            ¡Hola, 
            <?php printf($_SESSION["username"]); ?> 👋
        </h1>
        <nav>
            <ul>
                <li><a href="#">Inicio</a></li>
                <li><a href="#">Solicitudes</a></li>
                <li><a href="#">Guardados</a></li>
                <li><a href="#">Biblioteca</a></li>
            </ul>
        </nav>
    </header>

    <div class="search-bar">
        <input type="text" placeholder="Buscar libros">
    </div>

    <div class="categories">
        <button class="active">Populares</button>
        <button>Best Seller</button>
        <button>Clásicos</button>
    </div>

    <div class="carousel-container">
        <button class="arrow left" onclick="moveCarousel(-1)">&#10094;</button>
        <div class="carousel">
            <?php
            while ($row = $Result->fetch(PDO::FETCH_ASSOC)) {
            ?>
                <!-- Tarjetas -->
                <div class="card-container">
                    <div class="card-image" style="background-image: url('<?php echo htmlspecialchars($row['img']); ?>');"></div>
                    <div class="card-overlay"></div>
                    <div class="card-favorite">
                        <img src="img/me gusta.png" alt="">
                    </div>
                    <div class="card-content">
                        <h3 class="card-title"><?php echo $row["nombre"] . " - Disponibles: " . $row["disponibles"]; ?></h3>
                        <div class="card-author"><?php echo $row["autor"]; ?></div>
                        <div class="card-rating">
                            <span>⭐ <?php echo $row["pt"]; ?></span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <button class="arrow right" onclick="moveCarousel(1)">&#10095;</button>
    </div>

    <script>
        const carousel = document.querySelector('.carousel');
        let scrollAmount = 0;

        function moveCarousel(direction) {
            const cardWidth = 220; // Ancho de cada tarjeta (220px)
            const visibleCards = 4; // Número de tarjetas visibles (ajústalo según el diseño)
            const maxScroll = carousel.children.length - visibleCards;

            // Calcula el desplazamiento
            scrollAmount += direction;

            // Restringe el desplazamiento dentro del rango válido
            if (scrollAmount < 0) scrollAmount = 0;
            if (scrollAmount > maxScroll) scrollAmount = maxScroll;

            // Aplica el desplazamiento
            const offset = scrollAmount * cardWidth;
            carousel.style.transform = `translateX(-${offset}px)`;
        }
    </script>
</body>
</html>
