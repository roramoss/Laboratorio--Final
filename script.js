document.addEventListener('DOMContentLoaded', () => {
    const images = ['src/carrusel5.webp', 'src/carrusel6.webp', 'src/carrusel7.webp', 'src/carrusel 4.webp']; 
    const slideshowContainer = document.querySelector('.slideshow-container');

    if(slideshowContainer) {
        images.forEach(imgSrc => {
            const img = document.createElement('img');
            img.src = imgSrc;
            img.alt = 'Mate en promoción';
            img.className = 'slideshow-img';
            slideshowContainer.appendChild(img);
        });

        let slideIndex = 0;
        const slides = document.querySelectorAll('.slideshow-img');

        function showSlides() {
            slides.forEach(slide => slide.style.display = 'none');
            slideIndex++;
            if (slideIndex > slides.length) slideIndex = 1;
            slides[slideIndex - 1].style.display = 'block';
            setTimeout(showSlides, 3000); 
        }

        if(slides.length > 0) showSlides();

        const nextBtn = document.querySelector('.next');
        const prevBtn = document.querySelector('.prev');

        if(nextBtn) {
            nextBtn.addEventListener('click', () => {
                slideIndex++;
                if (slideIndex > slides.length) slideIndex = 1;
                slides.forEach(slide => slide.style.display = 'none');
                slides[slideIndex - 1].style.display = 'block';
            });
        }
        
        if(prevBtn) {
            prevBtn.addEventListener('click', () => {
                slideIndex--;
                if (slideIndex < 1) slideIndex = slides.length;
                slides.forEach(slide => slide.style.display = 'none');
                slides[slideIndex - 1].style.display = 'block';
            });
        }
    }
});

let cart = [];

// Abrir y cerrar el menú lateral
function toggleCart() {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-overlay');
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
}

// Agregar producto
function addToCart(id, name, price, image) {
    const existingItem = cart.find(item => item.id === id);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ id, name, price, image, quantity: 1 });
    }

    updateCartUI();
    
    // Abrir carrito al agregar
    const sidebar = document.getElementById('cart-sidebar');
    if (!sidebar.classList.contains('active')) {
        toggleCart();
    }
}

// Eliminar producto
function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    updateCartUI();
}

// Actualizar visualización
function updateCartUI() {
    const container = document.getElementById('cart-items-container');
    const totalSpan = document.getElementById('cart-total-price');
    const countSpan = document.getElementById('cart-count');
    
    container.innerHTML = '';
    let total = 0;
    let count = 0;

    cart.forEach(item => {
        total += item.price * item.quantity;
        count += item.quantity;

        const itemDiv = document.createElement('div');
        itemDiv.className = 'cart-item'; // Usar clase del CSS
        itemDiv.innerHTML = `
            <div style="display:flex; align-items:center;">
                <img src="${item.image}" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 5px;">
                <div>
                    <h4 style="margin: 0; font-size: 14px; color: #333;">${item.name}</h4>
                    <p style="margin: 5px 0 0; color: #666; font-size: 13px;">
                        $${item.price.toLocaleString('es-AR')} x ${item.quantity}
                    </p>
                </div>
            </div>
            <button onclick="removeFromCart(${item.id})" style="background: none; border: none; color: #e74c3c; cursor: pointer;">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(itemDiv);
    });

    totalSpan.innerText = total.toLocaleString('es-AR', {minimumFractionDigits: 2});
    countSpan.innerText = count;
}

// Enviar compra al servidor
async function procesarCompra() {
    if (cart.length === 0) {
        alert("Tu carrito está vacío.");
        return;
    }

    const btn = document.querySelector('.btn-checkout');
    const textoOriginal = btn.innerText;
    
    btn.disabled = true;
    btn.innerText = "Procesando...";

    try {
        const response = await fetch('guardar_compra.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(cart)
        });

        const data = await response.json();
           
        if (data.ok) {
            alert(`¡Felicidades por tu compra, ${data.usuario}! 🥳\n\nTu pedido ha sido registrado exitosamente.\nPronto prepararemos tu envío.`);
            cart = []; 
            updateCartUI(); 
            toggleCart();
        } else {
            alert("Error: " + data.mensaje);
            if(data.mensaje.includes("iniciar sesión")) {
                window.location.href = "login.php";
            }
        }

    } catch (error) {
        console.error(error);
        alert("Hubo un error de conexión.");
    } finally {
        btn.disabled = false;
        btn.innerText = textoOriginal;
    }
}