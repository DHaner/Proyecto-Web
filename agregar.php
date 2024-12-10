<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>registro</title>
</head>

<body>
  <div class="container-flex">

    <?php
    include("conexion.php");

    $Query = "INSERT INTO usuarios VALUES ('" . $_POST["usuario"] . "','" . $_POST["contraseña"] . "','" . $_POST["email"] . "','" . $_POST["tel"] . "')";

    if ($_POST["contraseña"] == $_POST["rcontraseña"]) {
      $Result = $conn->query($Query);  // se lanza la consulta

      if ($Result != null) {
    ?><script>
          alert("Se ha registrado con exito");
          window.location.href = "login.html";
        </script>
      <?php
      } else {
      ?><script>
          alert("¡UPS! algo salio mal, no se pudo registrar");
          window.location.href = "registro.html";
        </script>
      <?php
      }
    } else {
      ?><script>
        alert("Favor de verificar los datos, las contraseñas no coinciden");
        window.location.href = "registro.html";
      </script>
    <?php
    }
    ?>
  </div>
</body>

</html>