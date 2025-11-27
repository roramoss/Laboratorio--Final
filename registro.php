<?php
require 'conect.php'; 
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario_registro']);
    $password = trim($_POST['password_registro']);

    if ($usuario == "" || $password == "") {
        echo "Por favor completa todos los campos.";
        exit();
    }

    if (strlen($password) < 4) {
        echo "La contraseña debe tener al menos 4 caracteres.";
        exit();
    }

    // Verificar si el usuario ya existe
    $stmt = $con->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "El nombre de usuario ya está en uso.";
        exit();
    }

    // Encriptar contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario
    $stmt = $con->prepare("INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, 'user')");
    $stmt->bind_param("ss", $usuario, $passwordHash);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Usuario registrado correctamente.</p>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 1500);
              </script>";
        exit();
    } else {
        echo "Error al registrar.";
    }
}
?>
