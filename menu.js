// =========================================================
// FILTRAGE DYNAMIQUE PAR CATÉGORIE
// =========================================================

// Sélectionner tous les boutons de filtre
const filterButtons = document.querySelectorAll('.filter-btn');
const productCards = document.querySelectorAll('.product-card');

// Ajouter un événement à chaque bouton de filtre
filterButtons.forEach(button => {
    button.addEventListener('click', function() {
        // Retirer la classe active de tous les boutons
        filterButtons.forEach(btn => btn.classList.remove('active'));
        
        // Ajouter la classe active au bouton cliqué
        this.classList.add('active');
        
        // Récupérer la catégorie sélectionnée
        const selectedCategory = this.dataset.category;
        
        // Filtrer les produits
        productCards.forEach(card => {
            // Si "tous" est sélectionné, afficher toutes les cartes
            if (selectedCategory === 'tous') {
                card.style.display = 'block';
                card.style.animation = 'fadeInUp 0.6s ease-out';
            } 
            // Sinon, afficher seulement les cartes de la catégorie sélectionnée
            else if (card.dataset.category === selectedCategory) {
                card.style.display = 'block';
                card.style.animation = 'fadeInUp 0.6s ease-out';
            } 
            // Cacher les autres cartes
            else {
                card.style.display = 'none';
            }
        });
    });
});


// =========================================================
// TRI PAR PRIX OU NOM (Array.sort)
// =========================================================

const sortSelect = document.getElementById('sortSelect');
const productsGrid = document.getElementById('productsGrid');

sortSelect.addEventListener('change', function() {
    const sortValue = this.value;
    
    // Convertir NodeList en Array pour utiliser sort()
    const cardsArray = Array.from(productCards);
    
    // Trier selon l'option sélectionnée
    if (sortValue === 'price-asc') {
        // Tri par prix croissant
        cardsArray.sort((a, b) => {
            return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
        });
    } 
    else if (sortValue === 'price-desc') {
        // Tri par prix décroissant
        cardsArray.sort((a, b) => {
            return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
        });
    } 
    else if (sortValue === 'name') {
        // Tri par nom (A-Z)
        cardsArray.sort((a, b) => {
            return a.dataset.name.localeCompare(b.dataset.name);
        });
    }
    
    // Réorganiser les cartes dans la grille
    if (sortValue !== 'default') {
        cardsArray.forEach(card => {
            productsGrid.appendChild(card);
        });
    }
});


// =========================================================
// EFFET DE SURVOL DYNAMIQUE
// =========================================================

// Les détails supplémentaires s'affichent au survol grâce au CSS
// Mais on peut ajouter des animations JS supplémentaires

productCards.forEach(card => {
    const detailsHover = card.querySelector('.product-details-hover');
    
    if (detailsHover) {
        card.addEventListener('mouseenter', function() {
            // Animation supplémentaire au survol
            detailsHover.style.transition = 'all 0.4s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            // Réinitialiser l'animation
            detailsHover.style.transition = 'all 0.4s ease';
        });
    }
});


// =========================================================
// ANIMATION AU SCROLL
// =========================================================

// Observer pour animer les cartes quand elles entrent dans le viewport
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animation = 'fadeInUp 0.6s ease-out';
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observer chaque carte de produit
productCards.forEach(card => {
    observer.observe(card);
});


// =========================================================
// DÉFILEMENT FLUIDE POUR LA NAVIGATION
// =========================================================

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        // Si c'est une ancre interne (commence par #)
        if (href.startsWith('#')) {
            const target = document.querySelector(href);
            
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});


// =========================================================
// COMPTEUR DE PRODUITS AFFICHÉS
// =========================================================

function updateProductCount() {
    const visibleProducts = Array.from(productCards).filter(card => {
        return card.style.display !== 'none';
    });
    
    console.log(`Nombre de produits affichés: ${visibleProducts.length}`);
}

// Mettre à jour le compteur après chaque filtrage
filterButtons.forEach(button => {
    button.addEventListener('click', () => {
        setTimeout(updateProductCount, 100);
    });
});