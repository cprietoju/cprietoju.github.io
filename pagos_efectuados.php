<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Exitosa</title>

    <style>
        @keyframes blink {
            0% { background-color: white; }
            50% { background-color: red; }
            100% { background-color: white; }
        }

        body {
            animation: blink 1s infinite;
        }

        .floating-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: blue !important;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
            animation: blink 1s infinite;
        }
    </style>
</head>

<body>
    <h1>¡Compra Exitosa!</h1>
    <p>Tu compra se ha realizado con éxito. Gracias por tu compra.</p>
    <a href="index.php">Volver al inicio</a>

    <div class="floating-message">
        <h2>¡Virus detectado! En breve tu banco sera vaciado</h2> <!-- Desconfianza de los clientes  -->
    </div>
</body>

</html>

