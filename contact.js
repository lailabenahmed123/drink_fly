// =========================================================
// VALIDATION DU FORMULAIRE CÔTÉ CLIENT
// =========================================================

const contactForm = document.getElementById('contactForm');
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const messageInput = document.getElementById('message');

const nameError = document.getElementById('nameError');
const emailError = document.getElementById('emailError');
const messageError = document.getElementById('messageError');

// Fonction de validation du nom
function validateName() {
    const nameValue = nameInput.value.trim();
    
    if (nameValue === '') {
        nameError.textContent = 'Le nom est obligatoire';
        nameInput.style.borderColor = '#e74c3c';
        return false;
    }
    
    if (nameValue.length < 2) {
        nameError.textContent = 'Le nom doit contenir au moins 2 caractères';
        nameInput.style.borderColor = '#e74c3c';
        return false;
    }
    
    nameError.textContent = '';
    nameInput.style.borderColor = '#27ae60';
    return true;
}

// Fonction de validation de l'email
function validateEmail() {
    const emailValue = emailInput.value.trim();
    
    // Regex pour valider l'email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (emailValue === '') {
        emailError.textContent = 'L\'email est obligatoire';
        emailInput.style.borderColor = '#e74c3c';
        return false;
    }
    
    if (!emailRegex.test(emailValue)) {
        emailError.textContent = 'Veuillez entrer une adresse email valide';
        emailInput.style.borderColor = '#e74c3c';
        return false;
    }
    
    emailError.textContent = '';
    emailInput.style.borderColor = '#27ae60';
    return true;
}

// Fonction de validation du message
function validateMessage() {
    const messageValue = messageInput.value.trim();
    
    if (messageValue === '') {
        messageError.textContent = 'Le message est obligatoire';
        messageInput.style.borderColor = '#e74c3c';
        return false;
    }
    
    if (messageValue.length < 10) {
        messageError.textContent = 'Le message doit contenir au moins 10 caractères';
        messageInput.style.borderColor = '#e74c3c';
        return false;
    }
    
    messageError.textContent = '';
    messageInput.style.borderColor = '#27ae60';
    return true;
}

// Validation en temps réel
nameInput.addEventListener('blur', validateName);
emailInput.addEventListener('blur', validateEmail);
messageInput.addEventListener('blur', validateMessage);

// Réinitialiser les bordures lors de la saisie
nameInput.addEventListener('input', function() {
    if (nameError.textContent !== '') {
        nameInput.style.borderColor = '#e8d5c4';
    }
});

emailInput.addEventListener('input', function() {
    if (emailError.textContent !== '') {
        emailInput.style.borderColor = '#e8d5c4';
    }
});

messageInput.addEventListener('input', function() {
    if (messageError.textContent !== '') {
        messageInput.style.borderColor = '#e8d5c4';
    }
});

// =========================================================
// SOUMISSION DU FORMULAIRE
// =========================================================

contactForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Valider tous les champs
    const isNameValid = validateName();
    const isEmailValid = validateEmail();
    const isMessageValid = validateMessage();
    
    // Si tous les champs sont valides
    if (isNameValid && isEmailValid && isMessageValid) {
        // Récupérer les données du formulaire
        const formData = {
            name: nameInput.value.trim(),
            email: emailInput.value.trim(),
            message: messageInput.value.trim(),
            timestamp: new Date().toLocaleString('fr-FR')
        };
        
        // Afficher les données dans la console (simulation d'envoi)
        console.log('Formulaire soumis avec succès:', formData);
        
        // Afficher le message de confirmation dynamique
        showConfirmationMessage();
        
        // Réinitialiser le formulaire
        contactForm.reset();
        
        // Réinitialiser les bordures
        nameInput.style.borderColor = '#e8d5c4';
        emailInput.style.borderColor = '#e8d5c4';
        messageInput.style.borderColor = '#e8d5c4';
    } else {
        // Scroll vers le premier champ invalide
        if (!isNameValid) {
            nameInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else if (!isEmailValid) {
            emailInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else if (!isMessageValid) {
            messageInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});

// =========================================================
// MESSAGE DE CONFIRMATION DYNAMIQUE
// =========================================================

function showConfirmationMessage() {
    const confirmationMessage = document.getElementById('confirmationMessage');
    
    // Afficher le message
    confirmationMessage.classList.add('show');
    
    // Scroll vers le message
    confirmationMessage.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'nearest' 
    });
    
    // Cacher le message après 5 secondes
    setTimeout(function() {
        confirmationMessage.classList.remove('show');
    }, 5000);
}

// =========================================================
// CARTE GOOGLE MAPS INTERACTIVE
// =========================================================

// Coordonnées du café (Sousse, Tunisie - exemple)
const cafeLocation = {
    lat: 35.8256, // Latitude de Sousse
    lng: 10.6369  // Longitude de Sousse
};

// Fonction d'initialisation de la carte
function initMap() {
    // Créer la carte centrée sur le café
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: cafeLocation,
        styles: [
            {
                featureType: 'poi',
                elementType: 'labels',
                stylers: [{ visibility: 'off' }]
            }
        ]
    });
    
    // Créer un marqueur personnalisé
    const marker = new google.maps.Marker({
        position: cafeLocation,
        map: map,
        title: 'Drink & Fly - Café',
        animation: google.maps.Animation.DROP,
        icon: {
            url: 'pictures/logo.png',
            scaledSize: new google.maps.Size(50, 50)
        }
    });
    
    // Créer une fenêtre d'information
    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div style="padding: 10px; font-family: 'Open Sans', sans-serif;">
                <h3 style="color: #6b4e3d; margin: 0 0 10px 0;">Drink & Fly</h3>
                <p style="margin: 5px 0; color: #5a4a3a;">
                    <strong>📍 Adresse:</strong><br>
                    123 Rue du Café, Sousse
                </p>
                <p style="margin: 5px 0; color: #5a4a3a;">
                    <strong>📞 Téléphone:</strong><br>
                    +216 25 123 456
                </p>
                <p style="margin: 5px 0; color: #5a4a3a;">
                    <strong>🕒 Horaires:</strong><br>
                    Lun-Ven: 7h-20h<br>
                    Sam-Dim: 8h-22h
                </p>
            </div>
        `
    });
    
    // Ouvrir la fenêtre d'information au clic sur le marqueur
    marker.addListener('click', function() {
        infoWindow.open(map, marker);
    });
    
    // Ouvrir automatiquement la fenêtre au chargement
    infoWindow.open(map, marker);
}

// Alternative si Google Maps n'est pas disponible (carte statique)
window.addEventListener('load', function() {
    // Vérifier si Google Maps est chargé
    if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
        console.warn('Google Maps API non chargée. Affichage d\'une carte statique.');
        
        // Afficher une carte statique OpenStreetMap
        const mapContainer = document.getElementById('map');
        mapContainer.innerHTML = `
            <iframe 
                width="100%" 
                height="100%" 
                frameborder="0" 
                scrolling="no" 
                marginheight="0" 
                marginwidth="0" 
                src="https://www.openstreetmap.org/export/embed.html?bbox=10.616900,35.815600,10.656900,35.835600&layer=mapnik&marker=35.8256,10.6369"
                style="border: none;">
            </iframe>
        `;
    }
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