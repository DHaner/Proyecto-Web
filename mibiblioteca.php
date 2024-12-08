<?php
session_start();
include("conexion.php");
$usuario = $_SESSION['username'];

// Consulta para obtener solo los libros del usuario
$Query = $conn->prepare("SELECT * FROM libro WHERE dueño = :usuario");
$Query->bindParam(':usuario', $usuario, PDO::PARAM_STR);
$Query->execute();  // Ejecutar la consulta
$libros = $Query->fetchAll(PDO::FETCH_ASSOC); // Obtener resultados
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Virtual</title>
    <link rel="stylesheet" href="css/biblioteca.css">
</head>

<body>
    <header>
        <h1>¡Hola, <?php echo htmlspecialchars($_SESSION["username"]); ?> 👋</h1>
        <nav>
            <ul>
                <li><a href="home.php">Inicio</a></li>
                <li><a href="solicitudes.php">Solicitudes</a></li>
                <li><a href="guardados.php">Guardados</a></li>
                <li><a href="mibiblioteca.php" style="color: #ff5252;">Biblioteca</a></li>
            </ul>
        </nav>
        <div class="search-bar">
            <input type="text" id="search-input" placeholder="Buscar libros">
            <div class="dropdown">
                <button class="dropdown-toggle" onclick="toggleDropdown()">
                    <img src="img/filtro.png" alt="Filtros">
                </button>
                <div class="dropdown-menu" id="dropdown-menu">
                    <label>
                        <input type="radio" name="filter-type" value="titulo" checked onclick="closeDropdown()"> Título
                    </label>
                    <label>
                        <input type="radio" name="filter-type" value="autor" onclick="closeDropdown()"> Autor
                    </label>
                    <label>
                        <input type="radio" name="filter-type" value="genero" onclick="closeDropdown()"> Género
                    </label>
                    <label>
                        <input type="checkbox" id="filter-available" onclick="closeDropdown()"> Disponibles
                    </label>
                </div>
            </div>
        </div>
        <div class="add-book-button">
            <a href="agregarlibro.php" class="button">Agregar libro</a>
        </div>
    </header>

    <h1>Mis Libros</h1>

    <div class="carousel-container">
        <button class="arrow left" onclick="moveCarousel(-1)">&#10094;</button>
        <div class="carousel">
            <?php foreach ($libros as $libro): ?>
                <a href="libro.php?nombre=<?php echo urlencode($libro['nombre']); ?>" class="card-link">
                    <div class="card-container" 
                         data-titulo="<?php echo strtolower(htmlspecialchars($libro['nombre'])); ?>" 
                         data-autor="<?php echo strtolower(htmlspecialchars($libro['autor'])); ?>" 
                         data-genero="<?php echo strtolower(htmlspecialchars($libro['genero'] ?? '')); ?>" 
                         data-disponibles="<?php echo (int) $libro['disponible']; ?>">
                        <div class="card-image" style="background-image: url('<?php echo htmlspecialchars($libro['img']); ?>');"></div>
                        <div class="card-overlay"></div>
                        <div class="card-favorite">
                            <form method="POST">
                                <input type="hidden" name="accion" value="guardar_favorito">
                                <input type="hidden" name="libro" value="<?php echo htmlspecialchars($libro['nombre']); ?>">
                                <button type="submit" style="background: none; border: none; padding: 0;">
                                    <img src="img/me gusta.png" alt="Me gusta">
                                </button>
                            </form>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?php echo htmlspecialchars($libro["nombre"]); ?></h3>
                            <div class="card-author"><?php echo htmlspecialchars($libro["autor"]); ?></div>
                            <div class="card-rating">
                                <span>⭐ <?php echo isset($libro["pt"]) ? round($libro["pt"], 1) : 'N/A'; ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <button class="arrow right" onclick="moveCarousel(1)">&#10095;</button>
    </div>

    <script>
        function toggleDropdown() {
            const menu = document.getElementById('dropdown-menu');
            menu.classList.toggle('active');
        }

        function closeDropdown() {
            const menu = document.getElementById('dropdown-menu');
            menu.classList.remove('active');
        }

        document.getElementById('search-input').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                searchBooks();
            }
        });

        function searchBooks() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const filterType = document.querySelector('input[name="filter-type"]:checked').value;
            const filterAvailable = document.getElementById('filter-available').checked;
            const cards = document.querySelectorAll('.card-container');

            cards.forEach(card => {
                const title = card.dataset.titulo;
                const author = card.dataset.autor;
                const genre = card.dataset.genero;
                const available = parseInt(card.dataset.disponibles, 10) > 0;

                let matchesSearch = false;

                if (filterType === "titulo" && title.includes(searchTerm)) matchesSearch = true;
                if (filterType === "autor" && author.includes(searchTerm)) matchesSearch = true;
                if (filterType === "genero" && genre.includes(searchTerm)) matchesSearch = true;

                if (filterAvailable && !available) matchesSearch = false;

                card.parentElement.style.display = matchesSearch ? 'block' : 'none';
            });
        }
    </script>

</body>

</html>
