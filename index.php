<?php
date_default_timezone_set('America/Santiago'); // proporcionando horario actual de Santiago/Chile para la base de datos
include 'registrosMysql.php';
session_start();
$id_usuario = $_SESSION['id_usuario']; // Inicio de sesión del usuario

if (!isset($id_usuario)) {
   header('location:login.php'); // Verificar si el usuario esta registrado y si no puede iniciar sesión 
   exit();
}

if (isset($_GET['Salir'])) { 
   unset($id_usuario);
   session_destroy();        // Controlar la acción de salida del usuario
   header('location:login.php');
   exit();
}

// Capturar parametros a travez de una solicitud "POST"
$mensaje = array();

if (isset($_POST['añadir_al_carro'])) {
   

   $nombre_producto = $_POST['nombre_producto'];
   $precio_producto = $_POST['precio_producto'];
   $imagen_producto = $_POST['imagen_producto'];
   $cantidad_producto = $_POST['cantidad_producto'];

// Seleccionar el producto que se quiere añadir y despues este producto sera registrado automaticamente en la base de datos   
   $seleccionar_carrito = mysqli_query($conexion, "SELECT * FROM `carrito` WHERE nombre = '$nombre_producto' AND id_usuario = '$id_usuario'") or die('query failed');

   if (mysqli_num_rows($seleccionar_carrito) > 0) {
      $mensaje[] = '¡Este producto ya se añadió al carrito!';
   } else {
      mysqli_query($conexion, "INSERT INTO `carrito`(id_usuario, nombre, precio, imagen, cantidad) VALUES('$id_usuario', '$nombre_producto', '$precio_producto', '$imagen_producto', '$cantidad_producto')") or die('fallo en el sistema');
      $mensaje[] = '¡El producto fue añadido al carrito!';
   }
}

// Actualizar las modificaciones que se quieren hacer en el carrito. Luego estas modificaciones se verán en la base de datos `carrito`
if (isset($_POST['actualizar_carrito'])) {
   $actualizar_cantidad = $_POST['cantidad_carrito'];
   $actualizar_id = $_POST['id_carrito'];
   mysqli_query($conexion, "UPDATE `carrito` SET cantidad = '$actualizar_cantidad' WHERE id = '$actualizar_id'") or die('fallo en el sistema');
   $mensaje[] = '¡La cantidad fue actualizada!';
}

// Se remueven los datos seleccionados del carrito para que luego esta información se borre de la tabla `carrito`
if (isset($_GET['remover'])) {
   $remover_id = $_GET['remover'];
   mysqli_query($conexion, "DELETE FROM `carrito` WHERE id = '$remover_id'") or die('fallo en el sistema');
   header('location:index.php');
   exit();
}

// Borrar el contenido completo de la tabla `carrito`
if (isset($_GET['borrar_todo'])) {
   mysqli_query($conexion, "DELETE FROM `carrito` WHERE id_usuario = '$id_usuario'") or die('fallo del sistema');
   header('location:index.php');
   exit();
}

// Pagar los prodcutos y luego provocar el mensaje de alerta de virus en el archivo pagos_efectuados.php
if (isset($_POST['ir_a_pagar'])) {
   $consulta_carrito = mysqli_query($conexion, "SELECT * FROM `carrito` WHERE id_usuario = '$id_usuario'") or die('fallo del sistema');

   while ($fila_carrito = mysqli_fetch_assoc($consulta_carrito)) {
      $nombre_producto = $fila_carrito['nombre'];
      $precio_producto = $fila_carrito['precio'];
      $cantidad_producto = $fila_carrito['cantidad'];
      $fecha_registro = date('Y-m-d H:i:s');

      // Insertar los datos del producto en la tabla "registros_pagos"
      mysqli_query($conexion, "INSERT INTO `registros_pagos` (id_usuario, nombre_producto, precio_producto, cantidad_producto, fecha_registro) 
      VALUES ('$id_usuario', '$nombre_producto', '$precio_producto', '$cantidad_producto', '$fecha_registro')") or die('fallo en el sistema');
   }

   mysqli_query($conexion, "DELETE FROM `carrito` WHERE id_usuario = '$id_usuario'") or die('fallo del sistema');

   header('location: pagos_efectuados.php');
   exit();
}

$consulta_carrito = mysqli_query($conexion, "SELECT * FROM `carrito` WHERE id_usuario = '$id_usuario'") or die('fallo del sistema');
$total_carrito = mysqli_num_rows($consulta_carrito);

// Deshabilitar el boton "Ir a pagar" cuando no se añade nada al carrito 
$disabled = ($total_carrito > 0) ? '' : 'disabled';

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shopping cart</title>

   <!-- Estilos CSS  -->
   <link rel="stylesheet" href="estilo.css">

</head>

<body>

   <?php
   if (!empty($mensaje)) {
      foreach ($mensaje as $mensaje) {
         echo '<div class="message" onclick="this.remove();">' . $mensaje . '</div>';
      }
   }
   ?>

   <div class="container">

      <div class="user-profile">

         <?php
         $seleccionar_usuario = mysqli_query($conexion, "SELECT * FROM `tablaregistros` WHERE id = '$id_usuario'") or die('falla del sistema');
         if (mysqli_num_rows($seleccionar_usuario) > 0) {
            $fila_usuario = mysqli_fetch_assoc($seleccionar_usuario);
         }
         ?>

         <p> Usuario : <span><?php echo $fila_usuario['nombre']; ?></span> </p>
         <p> Correo : <span><?php echo $fila_usuario['correo']; ?></span> </p>
         <p> Tu clave es: <span><?php echo $fila_usuario['clave']; ?></span> </p> <!-- Peligro de uso malintencionado  -->
         <div class="flex">
            <a href="login.php" class="btn">Ingresar</a>
            <a href="registro.php" class="option-btn">Registrar</a>
            <a href="index.php?Salir=<?php echo $id_usuario; ?>" onclick="return confirm('¿Estás seguro que quieres salir?');" class="delete-btn">Salir</a>
         </div>

      </div>

      <div class="products">

         <h1 class="heading">Productos destacados</h1>

         <div class="box-container">

            <?php
            $seleccionar_producto = mysqli_query($conexion, "SELECT * FROM `productos`") or die('fallo del sistema');
            if (mysqli_num_rows($seleccionar_producto) > 0) {
               while ($fila_producto = mysqli_fetch_assoc($seleccionar_producto)) {
            ?>
                  <form method="post" class="box" action="">
                     <img src="imagenes/<?php echo $fila_producto['imagen']; ?>" alt="">
                     <div class="nombre"><?php echo $fila_producto['nombre']; ?></div>
                     <div class="precio">$<?php echo $fila_producto['precio']; ?></div>
                     <input type="number" min="1" name="cantidad_producto" value="1">
                     <input type="hidden" name="imagen_producto" value="<?php echo $fila_producto['imagen']; ?>">
                     <input type="hidden" name="nombre_producto" value="<?php echo $fila_producto['nombre']; ?>">
                     <input type="hidden" name="precio_producto" value="<?php echo $fila_producto['precio']; ?>">
                     <input type="submit" value="Añadir al carrito" name="añadir_al_carro" class="btn">
                  </form>
            <?php
               }
            };
            ?>

         </div>

      </div>

      <!-- SEGUIR AQUI DESPUES  -->
      <div class="shopping-cart">

         <h1 class="heading">carrito de compras</h1>

         <table>
            <thead>
               <th>imagen</th>
               <th>nombre</th>
               <th>precio</th>
               <th>cantidad</th>
               <th>precio total</th>
               <th>accion</th>
            </thead>
            <tbody>
               <?php
               $consulta_carrito = mysqli_query($conexion, "SELECT * FROM `carrito` WHERE id_usuario = '$id_usuario'") or die('fallo del sistema');
               $total_final = 0;
               if (mysqli_num_rows($consulta_carrito) > 0) {
                  while ($fila_carrito = mysqli_fetch_assoc($consulta_carrito)) {
                     $sub_total = intval($fila_carrito['precio']) * intval($fila_carrito['cantidad']);
                     $total_final += $sub_total;
               ?>
                     <tr>
                        <td><img src="imagenes/<?php echo $fila_carrito['imagen']; ?>" height="100" alt=""></td>
                        <td><?php echo $fila_carrito['nombre']; ?></td>
                        <td>$<?php echo $fila_carrito['precio']; ?></td>
                        <td>
                           <form action="" method="post">
                              <input type="hidden" name="id_carrito" value="<?php echo $fila_carrito['id']; ?>">
                              <input type="number" min="1" name="cantidad_carrito" value="<?php echo $fila_carrito['cantidad']; ?>">
                              <input type="submit" name="actualizar_carrito" value="update" class="option-btn">
                           </form>
                        </td>
                        <td>$<?php echo number_format($sub_total, 0, ',', '.'); ?>.000 </td>
                        <td><a href="index.php?remover=<?php echo $fila_carrito['id']; ?>" class="delete-btn" onclick="return confirm('¿Quieres remover el producto?');">remover</a></td>
                     </tr>
               <?php
                  }
               } else {
                  echo '<tr><td style="padding:20px; text-transform:capitalize;" colspan="6">no se añadio el producto</td></tr>';
               }
               ?>
               <tr class="table-bottom">
                  <td colspan="4">total final :</td>
                  <td>
                     <?php
                     if ($total_final > 100) {
                        $descuento = $total_final * 0.10; // Se deberia aplicar el 20% y no el 10%. Disminución de ingresos 
                        $total_final -= $descuento;
                        echo "$" . number_format($total_final, 0, ',', '.') . ".000 (20% de descuento)";
                     } else {
                        echo "$" . number_format($total_final, 0, ',', '.') . ".000";
                     }
                     ?>
                  </td>
                  <td><a href="index.php?borrar_todo" onclick="return confirm('¿Eliminar todo del carro?');" class="delete-btn <?php echo ($total_final > 1) ? '' : 'disabled'; ?>">eliminar todo</a></td>
               </tr>
            </tbody>
         </table>

         <div class="cart-btn">
            <form method="post">
               <input type="submit" class="btn <?php echo $disabled; ?>" value="Ir a pagar" name="ir_a_pagar">
            </form>

         </div>

      </div>

</body>

</html>