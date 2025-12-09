<?php
session_start();
include("conect.php");

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Debes iniciar sesión para comprar.']);
    exit;
}

// Obtener el carrito enviado por Javascript
$input = file_get_contents("php://input");
$carrito = json_decode($input, true);

if (empty($carrito)) {
    echo json_encode(['ok' => false, 'mensaje' => 'El carrito está vacío.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$fecha = date('Y-m-d H:i:s');
$total = 0;

// Validar stock y calcular total real
foreach ($carrito as $item) {
    $id_prod = (int)$item['id'];
    $consulta = mysqli_query($con, "SELECT precio, stock, nombre FROM productos WHERE id = $id_prod");
    $prodData = mysqli_fetch_assoc($consulta);

    if (!$prodData) {
        echo json_encode(['ok' => false, 'mensaje' => 'Producto no encontrado: ID ' . $id_prod]);
        exit;
    }
    
    if ($prodData['stock'] < $item['quantity']) {
        echo json_encode(['ok' => false, 'mensaje' => "Stock insuficiente para: " . $prodData['nombre']]);
        exit;
    }
    
    $total += $prodData['precio'] * $item['quantity'];
}

// Iniciar transacción
mysqli_begin_transaction($con);

try {
    // Insertar venta
    $queryVenta = "INSERT INTO ventas (id_usuario, fecha, total) VALUES ('$id_usuario', '$fecha', '$total')";
    if (!mysqli_query($con, $queryVenta)) {
        throw new Exception("Error al crear la venta: " . mysqli_error($con));
    }
    
    $id_venta = mysqli_insert_id($con);

    // Insertar detalles y restar stock
    foreach ($carrito as $item) {
        $id_prod = (int)$item['id'];
        $cantidad = (int)$item['quantity'];
        // Buscamos precio de nuevo para asegurar consistencia
        $consultaPrecio = mysqli_query($con, "SELECT precio FROM productos WHERE id = $id_prod");
        $filaPrecio = mysqli_fetch_assoc($consultaPrecio);
        $precio = $filaPrecio['precio'];
        
        $queryDetalle = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) 
                         VALUES ('$id_venta', '$id_prod', '$cantidad', '$precio')";
        if (!mysqli_query($con, $queryDetalle)) {
            throw new Exception("Error al insertar detalle.");
        }

        // Restar stock
        $queryStock = "UPDATE productos SET stock = stock - $cantidad WHERE id = $id_prod";
        if (!mysqli_query($con, $queryStock)) {
            throw new Exception("Error al actualizar stock.");
        }
    }
  echo json_encode([
        'ok' => true, 
        'id_venta' => $id_venta,
        'usuario' => $_SESSION['usuario'] 
    ]);
} catch (Exception $e) {
    mysqli_rollback($con);
    echo json_encode(['ok' => false, 'mensaje' => $e->getMessage()]);
}
?>