<?php

include 'registrosMysql.php';

if (isset($_POST['registrar'])) {

   $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
   $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
   $clave = mysqli_real_escape_string($conexion,$_POST['clave']);
   $repetirClave = mysqli_real_escape_string($conexion,$_POST['repetirClave']); // con md5 la clave queda encriptada

   $seleccionar = mysqli_query($conexion, "SELECT * FROM `tablaregistros` WHERE correo = '$correo' 
   AND clave = '$clave'") or die('falla del sistema');

   if (mysqli_num_rows($seleccionar) > 0) {
      $mensaje[] = '¡El usuario ya existe!';
   } else {
      mysqli_query($conexion, "INSERT INTO `tablaregistros`(nombre, correo, clave) 
      VALUES('$nombre', '$correo', '$clave')") or die('falla del sistema');
      $mensaje[] = '¡Registro exitoso!';
      header('location:login.php');
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Registro</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="estilo.css">

</head>

<body>

   <?php
   if (isset($mensaje)) {
      foreach ($mensaje as $mensaje) {
         echo '<div class="mensaje" onclick="this.remove();">' . $mensaje . '</div>';
      }
   }
   ?>

   <div class="form-container">

      <form action="" method="post">
         <h3>Registrate ahora</h3>
         <input type="text" name="nombre" required placeholder="Ingresar usuario" class="box">
         <input type="email" name="correo" required placeholder="Ingresar correo" class="box">
         <input type="password" name="clave" required placeholder="Introducir clave" class="box">
         <input type="password" name="repetirClave" required placeholder="Confirmar clave" class="box">
         <input type="submit" name="registrar" class="btn" value="Registrarse ahora">
         <p>¿Ya creaste una cuenta? <a href="login.php">Ingresar ahora</a></p>
      </form>

   </div>

</body>

</html>