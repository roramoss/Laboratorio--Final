<?php
session_start();
include("conect.php"); 

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$esAdmin = ($_SESSION['rol'] === 'admin');

$consultaProductos = mysqli_query($con, "SELECT * FROM productos ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="style.css"> 

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

    <div id="principal">  
        <h1>Gestión de Productos</h1>
    </div>

    <div style="text-align:right; margin:10px;">
        Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?> |
        <a href="logout.php" onclick="return confirm('¿Deseas cerrar sesión?');">Cerrar sesión</a>
    </div>

    <h2>Listado de Productos</h2>

    <div class="table-responsive">
        <table class="table">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Imagen</th>
                    <?php if($esAdmin): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>

            <tbody>
                <?php while($producto = mysqli_fetch_assoc($consultaProductos)): ?>
                <tr>
                    <td><?= $producto['id']; ?></td>
                    <td><?= $producto['nombre']; ?></td>
                    <td>$<?= number_format($producto['precio'], 2); ?></td>
                    <td><?= $producto['stock']; ?></td>

                    <td>
                        <?php if (!empty($producto['imagen'])): ?>
                            <img src="img/<?= $producto['imagen']; ?>" width="70">
                        <?php else: ?>
                            Sin imagen
                        <?php endif; ?>
                    </td>

                    <?php if($esAdmin): ?>
                    <td>
                        <a href="crear_producto.php" class="btn btn-success btn-sm">Agregar</a>
                        <a href="editar_producto.php?id=<?= $producto['id']; ?>" class="btn btn-warning btn-sm">Modificar</a>
                        <a href="eliminar_producto.php?id=<?= $producto['id']; ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('¿Eliminar este producto?');">Eliminar</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            </tbody>

        </table>
    </div>

</body>
</html>
