<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/css.css">
</head>

<body>
    <div class="container-flex">
    <?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
include("conexion.php");

$username = $_POST['usuario']; 
$password = $_POST['password'];


$Query = "SELECT * FROM usuarios WHERE user ='".$username."'";
$Result = $conn->query($Query);

if ($Result === false) {
    die("Error al ejecutar la consulta: " . $conn->error);
}
?>
<div id="contenedor"><?php
// Verificar si se encontró el usuario
// if ($Result->num_rows > 0) {
//     $row = $Result->fetch_assoc();

if ($Result->rowCount() > 0) {
    $row = $Result->fetch(); // Obtener la fila de resultados
    
    // Verificar la contraseña
   if (password_verify($password, $row["password"])) {
        // Contraseña correcta: Iniciar sesión y establecer variables de sesión
        $_SESSION['username'] = $username;
        header("Location: login.php");          // Redirigir a la página principal después de iniciar sesión
        exit;                                   // Finalizar el script después de redirigir
    } else if($password==$row["password"]) {
        $_SESSION['username'] = $username;
        header("Location: login.php");
        exit;
    } 
    else{
        // Contraseña incorrecta
        ?><script>alert("Contraseña incorrecta");</script>
        <?php
        header("Location: login.html")
    ?>
    <?php
    }
} else {
    // Usuario no encontrado
    ?><script>alert("Usuario no encontrado");</script>
        <?php
        header("Location: login.html")
?>
    <?php
    
    exit; // Finalizar el script después de redirigir
}?>
</div>

<?php
// Cerrar la conexión
// $mysql->close();
$conn = null;
?>
</div>
</body>

</html>