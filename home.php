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
                <li><a href="home.php">Inicio</a></li>
                <li><a href="solicitudes.php">Solicitudes</a></li>
                <li><a href="guardados.php">Guardados</a></li>
                <li><a href="biblioteca.php">Biblioteca</a></li>
            </ul>
        </nav>
    </header>

    <!-- Barra de búsqueda con filtros -->
    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Buscar libros">
        <select id="filter-type">
            <option value="titulo">Título</option>
            <option value="autor">Autor</option>
            <option value="genero">Género</option>
        </select>
        <label>
            <input type="checkbox" id="filter-available"> Disponibles
        </label>
        <button onclick="searchBooks()">Buscar</button>
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
                <a href="libro.php?nombre=<?php echo $row['nombre']; ?>" class="card-link">
                    <div class="card-container" data-titulo="<?php echo strtolower($row['nombre']); ?>" 
                         data-autor="<?php echo strtolower($row['autor']); ?>" 
                         data-genero="<?php echo strtolower($row['genero']); ?>" 
                         data-disponibles="<?php echo $row['disponibles']; ?>">
                        <div class="card-image" style="background-image: url('<?php echo htmlspecialchars($row['img']); ?>');"></div>
                        <div class="card-overlay"></div>
                        <div class="card-favorite">
                            <img src="img/me gusta.png" alt="Me gusta">
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?php echo $row["nombre"]; ?></h3>
                            <div class="card-author"><?php echo $row["autor"]; ?></div>
                            <div class="card-rating">
                                <span>⭐ <?php echo $row["pt"]; ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
        <button class="arrow right" onclick="moveCarousel(1)">&#10095;</button>
    </div>

    <script>
        function searchBooks() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const filterType = document.getElementById('filter-type').value;
            const filterAvailable = document.getElementById('filter-available').checked;
            const cards = document.querySelectorAll('.card-container');

            cards.forEach(card => {
                const title = card.dataset.titulo;
                const author = card.dataset.autor;
                const genre = card.dataset.genero;
                const available = parseInt(card.dataset.disponibles, 10) > 0;

                let matchesSearch = false;

                // Filtrar según el tipo seleccionado
                if (filterType === "titulo" && title.includes(searchTerm)) matchesSearch = true;
                if (filterType === "autor" && author.includes(searchTerm)) matchesSearch = true;
                if (filterType === "genero" && genre.includes(searchTerm)) matchesSearch = true;

                // Comprobar disponibilidad si está marcada la opción
                if (filterAvailable && !available) matchesSearch = false;

                // Mostrar u ocultar tarjeta
                card.parentElement.style.display = matchesSearch ? 'block' : 'none';
            });
        }

        function moveCarousel(direction) {
            const carousel = document.querySelector('.carousel');
            const cardWidth = 220; // Ancho de cada tarjeta (220px)
            const visibleCards = 4; // Número de tarjetas visibles (ajústalo según el diseño)
            const maxScroll = carousel.children.length - visibleCards;

            let scrollAmount = parseInt(carousel.dataset.scroll || 0);
            scrollAmount += direction;

            // Restringe el desplazamiento dentro del rango válido
            if (scrollAmount < 0) scrollAmount = 0;
            if (scrollAmount > maxScroll) scrollAmount = maxScroll;

            // Aplica el desplazamiento
            const offset = scrollAmount * cardWidth;
            carousel.style.transform = `translateX(-${offset}px)`;
            carousel.dataset.scroll = scrollAmount;
        }
    </script>
</body>

</html>
