<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/css.css">
<title>Registro</title>
</head>

<body>
<div class="container-flex">

<?php 


include(conexion.php);	

$Query= "INSERT INTO usuarios VALUES ('".$_POST["email"]."','".$_POST["usuario"]."','".$_POST["tel"]."','".$_POST["contraseña"]."','".$_POST["rcontraseña"]."'])";

if(.$_POST["contraseña"] == .$_POST["rcontraseña"]){
$Result = $oMysql->query( $Query );  // se lanza la consulta

if($Result!=null){
?><script>alert("Se ha registrado con exito");
window.location.href="login.html";
</script>
<?php
}else{
    ?><script>alert("¡UPS! algo salio mal, no se pudo registrar");
    window.location.href="registro.html";
    </script>
    <?php
}
}else{
    ?><script>alert("Favor de verificar los datos, las contraseñas no coinciden");
    window.location.href="registro.html";
    </script>
<?php
}
   ?>
</body>
</html>
