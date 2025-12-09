<?php
session_start();
include("conect.php"); 

// Obtener todos los productos
$consultaProductos = mysqli_query($con, "SELECT * FROM productos ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mateando - Tienda de Mates</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="./style.css?v=1">
    <script src="script.js?v=2"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="logo">Mateando</a>
    <ul class="nav-links">
        <li><a href="login.php">Iniciar Sesion</a></li>
        <li><a href="login.php">Registro</a></li>
        
        <li class="cart-trigger" onclick="toggleCart()" style="list-style: none; cursor: pointer;">
            <span class="material-symbols-outlined">shopping_cart</span>
            <span id="cart-count">0</span>
        </li>
    </ul>
</nav>

<header id="inicio">
    <p>Tu lugar para encontrar los mejores mates.</p>
</header>

<section class="menu-secciones">
    <a href="./index.html" class="btn-seccion">Volver Al Inicio</a>
</section>

<main>
    <section id="productos">
        <h2>Nuestros Productos</h2>

        <div class="contenedor-productos">

            <?php while($producto = mysqli_fetch_assoc($consultaProductos)): ?>
                <div class="tarjeta-producto">
                    <?php if ($producto['nuevo'] ?? false): ?>
                        <div class="etiqueta-estado">NUEVO</div>
                    <?php endif; ?>

                    <img src="img/<?= htmlspecialchars($producto['imagen']); ?>" alt="<?= htmlspecialchars($producto['nombre']); ?>">
                    
                    <h3 class="nombre-producto"><?= htmlspecialchars($producto['nombre']); ?></h3>

                    <div class="precios">
                        <?php if($producto['precio_tachado'] ?? 0): ?>
                            <span class="precio-tachado">$<?= number_format($producto['precio_tachado'], 2, ',', '.'); ?></span>
                        <?php endif; ?>
                        <span class="precio-oferta">$<?= number_format($producto['precio'], 2, ',', '.'); ?></span>
                    </div>

                    <p class="precio-alternativo">
                        $<?= number_format($producto['precio_transferencia'] ?? $producto['precio'], 2, ',', '.'); ?> con Transferencia o depósito
                    </p>

                    <button class="btn-carrito" onclick="addToCart(
                        <?= $producto['id']; ?>, 
                        '<?= htmlspecialchars($producto['nombre'], ENT_QUOTES); ?>', 
                        <?= $producto['precio']; ?>, 
                        'img/<?= htmlspecialchars($producto['imagen']); ?>'
                    )">
                        <i class="fas fa-cart-plus"></i> Añadir al Carrito
                    </button>
                    
                </div>
            <?php endwhile; ?>

        </div>
    </section>
</main>

<footer>
    <div class="footer-contenido">
        <p>&copy; 2025 Mateando. Todos los derechos reservados.</p>
        <div class="redes-sociales">
            <a href="#">Facebook</a>
            <a href="#">Instagram</a>
        </div>
    </div>
</footer>

<div id="cart-overlay" class="cart-overlay" onclick="toggleCart()"></div>
<div id="cart-sidebar" class="cart-sidebar">
    <div class="cart-header">
        <h3>Tu Carrito <i class="fas fa-shopping-basket"></i></h3>
        <span class="close-cart" onclick="toggleCart()">&times;</span>
    </div>
    <div id="cart-items-container" class="cart-items">
        </div>
    <div class="cart-footer">
        <h3>Total: $<span id="cart-total-price">0.00</span></h3>
        
        <button class="btn-checkout" onclick="procesarCompra()">Iniciar Compra</button>
        
    </div>
</div>

<script src="script.js"></script>

</body>
</html>