document.addEventListener('DOMContentLoaded', () => {
    // Array con las rutas de las imágenes que quieres en el carrusel
    const images = ['src/carrusel5.webp', 'src/carrusel6.webp', 'src/carrusel7.webp', 'src/carrusel 4.webp']; // Asegúrate de tener estas imágenes

    // Selecciona el contenedor del carrusel
    const slideshowContainer = document.querySelector('.slideshow-container');

    // Crea dinámicamente las imágenes y agrégalas al carrusel
    images.forEach(imgSrc => {
        const img = document.createElement('img');
        img.src = imgSrc;
        img.alt = 'Mate en promoción';
        img.className = 'slideshow-img';
        slideshowContainer.appendChild(img);
    });

    // Ahora, el código del carrusel se ejecuta después de que las imágenes se crearon
    let slideIndex = 0;
    const slides = document.querySelectorAll('.slideshow-img');

    function showSlides() {
        slides.forEach(slide => slide.style.display = 'none');
        slideIndex++;
        if (slideIndex > slides.length) {
            slideIndex = 1;
        }
        slides[slideIndex - 1].style.display = 'block';
        setTimeout(showSlides, 3000); // Cambia de imagen cada 3 segundos
    }

    // Iniciar el carrusel una vez que las imágenes se hayan agregado
    showSlides();
    
    // Lógica para los botones del carrusel
    document.querySelector('.next').addEventListener('click', () => {
        slideIndex++;
        if (slideIndex > slides.length) {
            slideIndex = 1;
        }
        slides.forEach(slide => slide.style.display = 'none');
        slides[slideIndex - 1].style.display = 'block';
    });
    
    document.querySelector('.prev').addEventListener('click', () => {
        slideIndex--;
        if (slideIndex < 1) {
            slideIndex = slides.length;
        }
        slides.forEach(slide => slide.style.display = 'none');
        slides[slideIndex - 1].style.display = 'block';
    });
});