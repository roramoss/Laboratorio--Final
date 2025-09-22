document.addEventListener('DOMContentLoaded', () => {
   
    const images = ['src/carrusel5.webp', 'src/carrusel6.webp', 'src/carrusel7.webp', 'src/carrusel 4.webp']; 

   
    const slideshowContainer = document.querySelector('.slideshow-container');

   
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
        if (slideIndex > slides.length) {
            slideIndex = 1;
        }
        slides[slideIndex - 1].style.display = 'block';
        setTimeout(showSlides, 3000); 
    }

    
    showSlides();
    
   
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