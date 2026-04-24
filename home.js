// =========================================================
// 1. SLIDESHOW AUTOMATIQUE
// =========================================================
let slideIndex = 0;

function showSlides() {
    const slides = document.querySelectorAll(".slide");
    
    if (slides.length === 0) return;

    // Cacher toutes les images
    slides.forEach(slide => slide.classList.remove("active"));

    // Passer à l'image suivante
    slideIndex++;
    if (slideIndex > slides.length) slideIndex = 1;

    // Afficher l'image actuelle
    slides[slideIndex - 1].classList.add("active");

    // Changer toutes les 4 secondes
    setTimeout(showSlides, 4000);
}

// Démarrer au chargement de la page
document.addEventListener('DOMContentLoaded', showSlides);


// =========================================================
// 2. DÉFILEMENT FLUIDE
// =========================================================
document.querySelectorAll('.nav-link, .cta-button').forEach(link => {
    link.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});


// =========================================================
// 3. ANIMATION BOUTON CTA
// =========================================================
const ctaButton = document.querySelector('.cta-button');

if (ctaButton) {
    ctaButton.addEventListener('mouseenter', function() {
        this.style.backgroundColor = '#4a3828';
        this.style.transform = 'scale(1.1) translateY(-5px)';
    });

    ctaButton.addEventListener('mouseleave', function() {
        this.style.backgroundColor = '#6b4e3d';
        this.style.transform = 'scale(1) translateY(0)';
    });
}


// =========================================================
// 4. CALCUL DE LA FACTURE
// =========================================================
const calculateBtn = document.getElementById('calculate-btn');
const checkboxes = document.querySelectorAll('.item-checkbox');

function calculateTotal() {
    const invoiceItems = document.getElementById('invoice-items');
    const subtotalText = document.getElementById('subtotal');
    const taxText = document.getElementById('tax');
    const totalText = document.getElementById('total');

    let subtotal = 0;
    invoiceItems.innerHTML = "";

    // Parcourir les articles cochés
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const price = parseFloat(checkbox.value);
            const itemName = checkbox.nextElementSibling.textContent.split(" - ")[0];
            
            subtotal += price;

            invoiceItems.innerHTML += `
                <div class="invoice-item">
                    <span>${itemName}</span>
                    <span>${price.toFixed(2)} DT</span>
                </div>
            `;
        }
    });

    // Message si aucun article
    if (subtotal === 0) {
        invoiceItems.innerHTML = '<p class="empty-message">Sélectionnez des articles pour voir la facture</p>';
    }

    // Calculer taxe et total
    const tax = subtotal * 0.10;
    const total = subtotal + tax;

    // Animer les valeurs
    animateValue(subtotalText, subtotal);
    animateValue(taxText, tax);
    animateValue(totalText, total);
}

if (calculateBtn) {
    calculateBtn.addEventListener('click', calculateTotal);
}


// =========================================================
// 5. ANIMATION DES NOMBRES
// =========================================================
function animateValue(element, endValue) {
    const duration = 500;
    const startValue = parseFloat(element.textContent) || 0;
    let startTime = null;

    function animation(currentTime) {
        if (!startTime) startTime = currentTime;

        const progress = Math.min((currentTime - startTime) / duration, 1);
        const currentValue = startValue + (endValue - startValue) * progress;

        element.textContent = currentValue.toFixed(2) + " DT";

        if (progress < 1) requestAnimationFrame(animation);
    }

    requestAnimationFrame(animation);
}


// =========================================================
// 6. ANIMATION AU SCROLL
// =========================================================
const sections = document.querySelectorAll('section:not(#home)');

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

sections.forEach(section => {
    section.style.opacity = "0";
    section.style.transform = "translateY(20px)";
    section.style.transition = "opacity 0.6s ease, transform 0.6s ease";
    observer.observe(section);
});