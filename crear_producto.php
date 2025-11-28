<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include("conect.php");

// Si viene un ID, estamos editando; si no, estamos agregando
$id = $_GET['id'] ?? null;
$producto = [
    'nombre' => '',
    'precio' => '',
    'stock' => '',
    'imagen' => ''
];

if ($id) {
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
}

$error = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = trim($_POST['precio'] ?? '');
    $stock = trim($_POST['stock'] ?? '');

    if (empty($nombre) || empty($precio) || empty($stock)) {
        $error = "Completa todos los campos obligatorios.";
    } else {
        // Manejar imagen
        $imagenNueva = $producto['imagen']; // mantener imagen anterior por defecto

        if (!empty($_FILES["imagen"]["name"])) {
            $archivo = $_FILES["imagen"];
            $ext = pathinfo($archivo["name"], PATHINFO_EXTENSION);
            $imagenNueva = uniqid("prod_") . "." . $ext;
            move_uploaded_file($archivo["tmp_name"], "img/" . $imagenNueva);

            // Borrar imagen vieja si existe
            if (!empty($producto["imagen"]) && file_exists("img/" . $producto["imagen"])) {
                unlink("img/" . $producto["imagen"]);
            }
        }

        if ($id) {
            // Editar producto existente
            $sql = "UPDATE productos SET nombre=?, precio=?, stock=?, imagen=? WHERE id=?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sdisi", $nombre, $precio, $stock, $imagenNueva, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Agregar nuevo producto
            $sql = "INSERT INTO productos (nombre, precio, stock, imagen) VALUES (?, ?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sdis", $nombre, $precio, $stock, $imagenNueva);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $id ? "Editar" : "Agregar"; ?> producto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2><?php echo $id ? "Editar" : "Agregar"; ?> producto</h2>

<?php if ($error): ?>
    <div style="color:red;"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <label>Nombre:</label><br>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required><br><br>

    <label>Precio:</label><br>
    <input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>" required><br><br>

    <label>Stock:</label><br>
    <input type="number" name="stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" required><br><br>

    <?php if ($id): ?>
        <label>Imagen actual:</label><br>
        <?php if (!empty($producto['imagen'])): ?>
            <img src="img/<?php echo $producto['imagen']; ?>" width="120"><br><br>
        <?php else: ?>
            <p>No tiene imagen</p>
        <?php endif; ?>
    <?php endif; ?>

    <label>Subir imagen <?php echo $id ? "(opcional)" : "(requerida)"; ?>:</label><br>
    <input type="file" name="imagen" accept="image/*" <?php echo $id ? "" : "required"; ?>><br><br>

    <button type="submit"><?php echo $id ? "Guardar cambios" : "Agregar producto"; ?></button>
</form>

<br>
<a href="index.php">⬅ Volver</a>

</body>
</html>
