<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "mates");

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

/* ------------------------------------------------------------------
   1️⃣ OBTENER LA IMAGEN DEL PRODUCTO ANTES DE BORRARLO
------------------------------------------------------------------ */
$sql = "SELECT imagen FROM productos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();
$stmt->close();

/* ------------------------------------------------------------------
   2️⃣ BORRAR LA IMAGEN FÍSICA SI EXISTE
------------------------------------------------------------------ */

if ($producto && !empty($producto['imagen'])) {
    $rutaImagen = "img/" . $producto['imagen'];

    if (file_exists($rutaImagen)) {
        unlink($rutaImagen); // elimina la imagen del servidor
    }
}

/* ------------------------------------------------------------------
   3️⃣ BORRAR EL PRODUCTO DE LA BASE DE DATOS
------------------------------------------------------------------ */
$sql = "DELETE FROM productos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: index.php");
exit();
?>
