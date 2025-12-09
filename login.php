<?php
session_start();
require_once('conect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST['usuario_login'] ?? '';
    $password = $_POST['password_login'] ?? '';

    if ($usuario === '' || $password === '') {
        $error = "Debe completar usuario y contraseña.";
    } else {
        $sql = "SELECT * FROM usuarios WHERE usuario = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['id_usuario'] = $user['id'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol'] = $user['rol'];

            if ($user['rol'] === 'admin') {
                header("Location: index.php");
            } else {
                header("Location: index.html");
            }
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
        $stmt->close();
    }
}
$con->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
     <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="./style.css?v=1">
</head>
<body>

<div class="container">

    <!-- Mensaje de error -->
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Formulario de Login -->
    <form action="login.php" method="POST" class="form">
        <span class="title">Ingresar</span>
        <label for="usuario_login" class="label">Usuario:</label>
        <input type="text" id="usuario_login" name="usuario_login" class="input" placeholder="Ingresar Usuario" required>
        <label for="password_login" class="label">Contraseña:</label>
        <input type="password" id="password_login" name="password_login" class="input" placeholder="Ingresar Contraseña" required>
        <button type="submit" class="submit">Ingresar</button>
    </form>

    <hr>

    <!-- Formulario de Registro -->
    <form action="registro.php" method="POST" class="form">
        <span class="title">Registro</span>
        <label for="usuario_registro" class="label">Usuario:</label>
        <input type="text" id="usuario_registro" name="usuario_registro" class="input" placeholder="Ingresar Usuario" required>
        <label for="password_registro" class="label">Contraseña:</label>
        <input type="password" id="password_registro" name="password_registro" class="input" placeholder="Ingresar Contraseña" required pattern=".{4,}" title="La contraseña debe tener al menos 4 caracteres">
        <button type="submit" class="submit">Registrarse</button>
    </form>

    <section class="submit">
  <a href="./index.html" class="submit">Volver Al Inicio</a>
        </section>

</div>

</body>
</html>
