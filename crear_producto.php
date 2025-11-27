<?php
session_start();

// Solo admin puede entrar
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require "conect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST['nombre'] ?? '');
    $precio = trim($_POST['precio'] ?? '');
    $stock = trim($_POST['stock'] ?? '');

    // VALIDACIÓN
    if ($nombre === "" || $precio === "" || $stock === "") {
        $error = "Por favor completa todos los campos obligatorios.";
    } else {

        // MANEJO DE IMAGEN
        $imagenNombre = "";

        if (!empty($_FILES["imagen"]["name"])) {
            $archivo = $_FILES["imagen"];
            $ext = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));

            $permitidas = ["jpg", "jpeg", "png", "webp"];

            if (!in_array($ext, $permitidas)) {
                $error = "Formato de imagen no permitido.";
            } else {
                $imagenNombre = uniqid("prod_") . "." . $ext;
                move_uploaded_file($archivo["tmp_name"], "img/" . $imagenNombre);
            }
        }

        if ($error === "") {

            // INSERTAR EN LA BD SIN DESCRIPCIÓN
            $sql = "INSERT INTO productos (nombre, precio, stock, imagen) 
                    VALUES (?, ?, ?, ?)";

            $stmt = $con->prepare($sql);
            $stmt->bind_param("siis", $nombre, $precio, $stock, $imagenNombre);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php");
            exit();
        }
    }
}
?>
