const contactForm = document.getElementById('contactForm');
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const messageInput = document.getElementById('message');
const nameError = document.getElementById('nameError');
const emailError = document.getElementById('emailError');
const messageError = document.getElementById('messageError');

function validateName() {
    if (nameInput.value.trim().length < 2) {
        nameError.textContent = 'Le nom doit contenir au moins 2 caractères';
        nameInput.style.borderColor = '#e74c3c';
        return false;
    }
    nameError.textContent = '';
    nameInput.style.borderColor = '#27ae60';
    return true;
}

function validateEmail() {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailInput.value.trim())) {
        emailError.textContent = 'Adresse email invalide';
        emailInput.style.borderColor = '#e74c3c';
        return false;
    }
    emailError.textContent = '';
    emailInput.style.borderColor = '#27ae60';
    return true;
}

function validateMessage() {
    if (messageInput.value.trim().length < 10) {
        messageError.textContent = 'Le message doit contenir au moins 10 caractères';
        messageInput.style.borderColor = '#e74c3c';
        return false;
    }
    messageError.textContent = '';
    messageInput.style.borderColor = '#27ae60';
    return true;
}

nameInput.addEventListener('blur', validateName);
emailInput.addEventListener('blur', validateEmail);
messageInput.addEventListener('blur', validateMessage);

contactForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const isNameValid = validateName();
    const isEmailValid = validateEmail();
    const isMessageValid = validateMessage();
    if (isNameValid && isEmailValid && isMessageValid) {
        contactForm.submit();
    }
});

window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
        const confirmationMessage = document.getElementById('confirmationMessage');
        if (confirmationMessage) {
            confirmationMessage.classList.add('show');
            setTimeout(function() {
                confirmationMessage.classList.remove('show');
            }, 5000);
        }
    }
});