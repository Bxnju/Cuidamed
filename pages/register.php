<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuidamed - Registro</title>
    <link rel="icon" href="../assets/icon.png" type="image/png">
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        .container {
            background: rgba(0, 19, 68, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 123, 190, 0.2);
            width: 50%;
            text-align: center;
        }

        .container>img {
            width: 100%;
            margin-bottom: 20px;
        }

        .container input[type="text"],
        .container input[type="email"],
        .container input[type="password"] {
            margin: 10px 0;
        }

        .container input:focus {
            border-color: #6bb2c5;
            outline: none;
        }

        .container button {
            width: 100%;
            padding: 12px 15px;
            margin-top: 10px;
            background-color: #6bb2c5;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .container button:hover {
            background-color: #579cab;
        }

        .container p {
            margin-top: 15px;
            font-size: 14px;
        }

        .container a {
            color: #6bb2c5;
            text-decoration: none;
            font-weight: bold;
        }

        .container a:hover {
            text-decoration: underline;
        }

        /* Responsive styles */
        @media screen and (max-width: 768px) {
            .container {
                width: 100%;
                padding: 1em;
            }

            .container input[type="email"],
            .container input[type="password"],
            .container input[type="text"],
            .container input[type="tel"] {
                font-size: 1.5em;
            }

            .container button {
                font-size: 1.5em;
            }

            .container p {
                font-size: 1.3em;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="../assets/logo.png" alt="">
        <form action="../php/register.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre completo" required>
            <input type="email" name="correo" placeholder="Correo electrónico" required>
            <input type="text" name="telefono" placeholder="Teléfono" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <button class="btn" type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</body>

</html>