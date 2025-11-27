<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include("conect.php");

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit();
}

// Obtener datos del producto actual
$sql = "SELECT * FROM productos WHERE id=?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();
$stmt->close();

if (!$producto) {
    echo "Producto no encontrado.";
    exit();
}

$error = "";

// Cuando se envía el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST['nombre'] ?? '');
    $precio = trim($_POST['precio'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $stock = trim($_POST['stock'] ?? '');

    if (empty($nombre) || empty($precio) || empty($stock)) {
        $error = "Completa todos los campos obligatorios.";
    } else {

        // Imagen nueva opcional
        $imagenNueva = $producto['imagen']; // deja la imagen anterior por defecto

        if (!empty($_FILES["imagen"]["name"])) {
            $archivo = $_FILES["imagen"];
            $ext = pathinfo($archivo["name"], PATHINFO_EXTENSION);

            // Nombre único para evitar conflictos
            $imagenNueva = uniqid("prod_") . "." . $ext;

            // Guardar nueva imagen
            move_uploaded_file($archivo["tmp_name"], "img/" . $imagenNueva);

            // Borrar imagen vieja si existía
            if (!empty($producto["imagen"]) && file_exists("img/" . $producto["imagen"])) {
                unlink("img/" . $producto["imagen"]);
            }
        }

        // Actualizar BD
        $sql = "UPDATE productos SET nombre=?, precio=?, descripcion=?, stock=?, imagen=? WHERE id=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sdsi si", $nombre, $precio, $descripcion, $stock, $imagenNueva, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar producto</title>
</head>
<body>

<h2>Editar producto</h2>

<?php if ($error): ?>
    <div style="color:red;"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <label>Nombre:</label><br>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required><br><br>

    <label>Precio:</label><br>
    <input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>" required><br><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea><br><br>

    <label>Stock:</label><br>
    <input type="number" name="stock" value="<?php echo $producto['stock']; ?>" required><br><br>

    <label>Imagen actual:</label><br>
    <?php if (!empty($producto['imagen'])): ?>
        <img src="img/<?php echo $producto['imagen']; ?>" width="120"><br><br>
    <?php else: ?>
        <p>No tiene imagen</p>
    <?php endif; ?>

    <label>Subir nueva imagen (opcional):</label><br>
    <input type="file" name="imagen" accept="image/*"><br><br>

    <button type="submit">Guardar cambios</button>
</form>

<br>
<a href="index.php">⬅ Volver</a>

</body>
</html>
