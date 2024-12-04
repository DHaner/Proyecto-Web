<?php
include("conexion.php");
session_start();

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $autor = $_POST['autor'];
    $puntaje = $_POST['pt'];
    $genero = $_POST['genero'];
    $sinopsis = $_POST['sinopsis'];
    $paginas = $_POST['pag'];
    $dueno = $_SESSION['username']; // Dueño del libro (usuario actual)

    // Verificar si el usuario ingresó un nuevo libro o autor
    if ($nombre == 'nuevo') {
        $nombre = $_POST['nombre_nuevo']; // Nuevo nombre de libro
    }
    if ($autor == 'nuevo') {
        $autor = $_POST['autor_nuevo']; // Nuevo autor
    }

    // Procesar la imagen
    $imagen = $_FILES['imagen'];
    if ($imagen['error'] === UPLOAD_ERR_OK) {
        // Directorio de destino
        $targetDir = 'uploads/';
        $imagenPath = $targetDir . basename($imagen['name']);

        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($imagen['tmp_name'], $imagenPath)) {
            // Preparar la consulta de inserción, incluyendo la columna "disponible"
            $query = "INSERT INTO libro (nombre, autor, pt, genero, sinopsis, pag, img, dueño, disponible) 
                      VALUES (:nombre, :autor, :pt, :genero, :sinopsis, :pag, :img, :dueno, 'SI')";
            $stmt = $conn->prepare($query);

            // Ejecutar la consulta de inserción
            if ($stmt->execute([
                ':nombre' => $nombre,
                ':autor' => $autor,
                ':pt' => $puntaje,
                ':genero' => $genero,
                ':sinopsis' => $sinopsis,
                ':pag' => $paginas,
                ':img' => $imagenPath,
                ':dueno' => $dueno
            ])) {
                // Mostrar alerta de éxito y redirigir
                echo "<script>alert('Libro agregado correctamente'); window.location.href = 'mibiblioteca.php';</script>";
            } else {
                // Error en la inserción en la base de datos
                echo "<script>alert('Error al guardar en la base de datos'); window.location.href = 'home.php';</script>";
            }
        } else {
            // Error al mover el archivo
            echo "<script>alert('Error al mover la imagen'); window.location.href = 'home.php';</script>";
        }
    } else {
        // Error en la carga de la imagen
        echo "<script>alert('Error al cargar la imagen: " . $imagen['error'] . "'); window.location.href = 'home.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Libro</title>
    <link rel="stylesheet" href="css/agregar.css">
</head>
<body>
    <div class="form-container">
        <h1>Agregar Libro</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div>
                <label for="nombre">Nombre del Libro:</label>
                <select name="nombre" id="nombre" required>
                    <option value="">Selecciona un libro</option>
                    <option value="nuevo">Ingresar nuevo libro</option>
                    <!-- Aquí debes cargar los libros existentes para que el usuario pueda seleccionarlos -->
                    <?php
                    $query = "SELECT nombre FROM libro";
                    $result = $conn->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . htmlspecialchars($row['nombre']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
                    }
                    ?>
                </select>
                <input type="text" name="nombre_nuevo" id="nombre_nuevo" placeholder="Nuevo nombre de libro" style="display:none;" />
            </div>

            <div>
                <label for="autor">Autor:</label>
                <select name="autor" id="autor" required>
                    <option value="">Selecciona un autor</option>
                    <option value="nuevo">Ingresar nuevo autor</option>
                    <!-- Aquí debes cargar los autores existentes para que el usuario pueda seleccionarlos -->
                    <?php
                    $query = "SELECT DISTINCT autor FROM libro";
                    $result = $conn->query($query);
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . htmlspecialchars($row['autor']) . "'>" . htmlspecialchars($row['autor']) . "</option>";
                    }
                    ?>
                </select>
                <input type="text" name="autor_nuevo" id="autor_nuevo" placeholder="Nuevo autor" style="display:none;" />
            </div>

            <div>
                <label for="pt">Puntaje:</label>
                <input type="number" name="pt" id="pt" min="0" max="5" required>
            </div>

            <div>
                <label for="genero">Género:</label>
                <input type="text" name="genero" id="genero" required>
            </div>

            <div>
                <label for="sinopsis">Sinopsis:</label>
                <textarea name="sinopsis" id="sinopsis" required></textarea>
            </div>

            <div>
                <label for="pag">Número de Páginas:</label>
                <input type="number" name="pag" id="pag" required>
            </div>

            <div>
                <label for="imagen">Imagen del Libro:</label>
                <input type="file" name="imagen" id="imagen" accept="image/*" required>
            </div>

            <div>
                <button type="submit">Agregar Libro</button>
            </div>
        </form>
        <a href="home.php"><button>Regresar</button></a>
    </div>

    <script>
        // Mostrar los campos de texto para ingresar nuevos valores si se selecciona la opción 'nuevo'
        document.getElementById('nombre').addEventListener('change', function () {
            if (this.value == 'nuevo') {
                document.getElementById('nombre_nuevo').style.display = 'block';
            } else {
                document.getElementById('nombre_nuevo').style.display = 'none';
            }
        });

        document.getElementById('autor').addEventListener('change', function () {
            if (this.value == 'nuevo') {
                document.getElementById('autor_nuevo').style.display = 'block';
            } else {
                document.getElementById('autor_nuevo').style.display = 'none';
            }
        });
    </script>
</body>
</html>
