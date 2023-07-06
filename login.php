<?php

include 'registrosMysql.php';
session_start();

if (isset($_POST['ingresar'])) {

   $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
   $clave = mysqli_real_escape_string($conexion, $_POST['clave']); // con md5 se encriptan las claves

   $seleccionar = mysqli_query($conexion, "SELECT * FROM `tablaregistros` WHERE correo = '$correo' AND 
   clave = '$clave'") or die('falla en el sistema');

   if (mysqli_num_rows($seleccionar) > 0) {
      $fila = mysqli_fetch_assoc($seleccionar);
      $_SESSION['id_usuario'] = $fila['id'];
      header('location:index.php');
   } else {
      $mensaje[] = 'Incorrecto. Por favor introducir nuevamente el correo y la contraseña';
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

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
         <h3>Ingresar ahora</h3>
         <input type="email" name="correo" required placeholder="Ingresa su email" class="box">
         <input type="password" name="clave" required placeholder="Ingresa tu clave" class="box">
         <input type="submit" name="ingresar" class="btn" value="Ingresar">
         <p>¿Aún no estás registrado? <a href="registro.php">Registrate ahora</a></p>
      </form>

   </div>

</body>

</html>