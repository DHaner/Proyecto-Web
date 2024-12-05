<?php
session_start();
include("conexion.php");

// Asegúrate de que la consulta esté correcta
$Query = "SELECT libro.* FROM libro INNER JOIN guardados ON libro.nombre = guardados.libro;"; 
$Result = $conn->query($Query);


$Query = "SELECT nombre, autor, genero, img, COUNT(*) as disponibles, AVG(pt) as promedio_pt
          FROM libro INNER JOIN guardados ON libro.nombre = guardados.libro
          WHERE disponible = 'SI' 
          GROUP BY nombre, autor, genero, img";

$Result = $conn->query($Query);

//===========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    if ($accion === 'guardar_favorito') {
        $usuario = $_SESSION['username']; // Usuario actual
        $libro = isset($_POST['libro']) ? htmlspecialchars($_POST['libro']) : '';

        // Verifica si los datos están completos
        if ($usuario && $libro) {
            try {
                // Inserta en la tabla guardados
                $queryGuardar = $conn->prepare("INSERT INTO guardados (usuario, libro) VALUES (:usuario, :libro)");
                $queryGuardar->bindParam(':usuario', $usuario, PDO::PARAM_STR);
                $queryGuardar->bindParam(':libro', $libro, PDO::PARAM_STR);

                if ($queryGuardar->execute()) {
                    echo "<script>alert('¡Libro guardado exitosamente!');window.location.href='home.php';</script>";
                } else {
                    echo "<script>alert('Error al guardar el libro. Inténtalo nuevamente.');window.location.href='home.php';</script>";
                }
            } catch (Exception $e) {
                echo "<script>alert('Error: " . $e->getMessage() . "');window.location.href='home.php';</script>";
            }
        } else {
            echo "<script>alert('Datos incompletos.');window.location.href='home.php';</script>";
        }
    }
}
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
        <h1>¡Hola, <?php printf($_SESSION["username"]); ?> 👋</h1>
        <nav>
            <ul>
                <li><a href="home.php">Inicio</a></li>
                <li><a href="solicitudes.php">Solicitudes</a></li>
                <li><a href="guardados.php" style="color: #ff5252;">Guardados</a></li>
                <li><a href="mibiblioteca.php">Biblioteca</a></li>
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
    </header>

    <h1>Libros disponibles</h1>

    <div class="carousel-container">
        <button class="arrow left" onclick="moveCarousel(-1)">&#10094;</button>
        <div class="carousel">
            <?php
            while ($row = $Result->fetch(PDO::FETCH_ASSOC)) {
            ?>
                <a href="libro.php?nombre=<?php echo $row['nombre']; ?>" class="card-link">
                    <div class="card-container" data-titulo="<?php echo strtolower($row['nombre']); ?>" 
                         data-autor="<?php echo strtolower($row['autor']); ?>" 
                         data-genero="<?php echo strtolower($row['genero']); ?>" 
                         data-disponibles="<?php echo $row['disponibles']; ?>">
                        <div class="card-image" style="background-image: url('<?php echo htmlspecialchars($row['img']); ?>');"></div>
                        <div class="card-overlay"></div>
                        <div class="card-favorite" style="background: rgba(255, 0, 0, 0.582);">
                        <form method="POST">
                            <input type="hidden" name="accion" value="guardar_favorito">
                            <input type="hidden" name="libro" value="<?php echo htmlspecialchars($row['nombre']); ?>">
                            <button type="submit" style="background: none; border: none; padding: 0;">
                            <img src="img/me gusta.png" alt="Me gusta">
                            </button>
                        </form>
                        </div>

                        <div class="card-content">
                            <h3 class="card-title"><?php echo $row["nombre"]; ?></h3>
                            <div class="card-author"><?php echo $row["autor"]; ?></div>
                            <div class="card-rating">
                                <span>⭐ <?php echo round($row["promedio_pt"], 1); ?></span>
                            </div>
                            <div class="card-available">
                                <span>Disponibles: <?php echo $row["disponibles"]; ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php } ?>
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
