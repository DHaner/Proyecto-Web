<!-- login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/css.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/css.css">
    <title>Iniciar sesión</title>
</head>
<body>
<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header("Location: login.html"); // Redirigir a la página de inicio de sesión
    exit;
}

header("Location: home.php"); // Redirigir a la página de inicio de sesión
    exit;
?>

</body>
</html>



