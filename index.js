document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('container');
    const registerBtn = document.getElementById('register');
    const loginBtn = document.getElementById('login');
    const form = document.getElementById('signupForm');
    const emailInput = document.getElementById('mail');
    const emailError = document.getElementById('email-error');
    const passwordInput = document.getElementById('signup-password');
    const passwordError = document.getElementById('password-error');

    // Gestion du formulaire de connexion
    const loginForm = document.getElementById('loginForm'); // Assurez-vous que l'ID du formulaire est bien "loginForm"
    const loginError = document.getElementById('login-error'); // Élément pour afficher l'erreur sous le formulaire de connexion

    registerBtn.addEventListener('click', () => {
        container.classList.add("active");
    });

    loginBtn.addEventListener('click', () => {
        container.classList.remove("active");
    });

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        emailError.style.display = 'none';
        emailError.textContent = '';
        passwordError.style.display = 'none';
        passwordError.textContent = '';

        const surname = document.getElementsByName('surname')[0].value.trim();
        const mail = emailInput.value.trim();
        const mdp = passwordInput.value.trim();

        if (surname === "") {
            emailError.textContent = "Le nom est requis.";
            emailError.style.display = 'block';
            return;
        }

        if (mail === "") {
            emailError.textContent = "L'email est requis.";
            emailError.style.display = 'block';
            return;
        }

        if (mdp === "") {
            passwordError.textContent = "Le mot de passe est requis.";
            passwordError.style.display = 'block';
            return;
        }

        const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailRegex.test(mail)) {
            emailError.textContent = "L'adresse email n'est pas valide.";
            emailError.style.display = 'block';
            return;
        }

        if (!validatePassword(mdp)) {
            passwordError.textContent = "Le mot de passe doit contenir au moins 8 caractères, 1 majuscule, 2 chiffres et 1 caractère spécial.";
            passwordError.style.display = 'block';
            return;
        }

        const formData = new FormData(form);

        fetch(form.action, {
            method: form.method,
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                if (data.field === 'mail') {
                    emailError.textContent = data.error;
                    emailError.style.display = 'block';
                } else if (data.field === 'mdp') {
                    passwordError.textContent = data.error;
                    passwordError.style.display = 'block';
                } else if (data.error.includes('email déjà utilisé')) {
                    emailError.textContent = data.error;
                    emailError.style.display = 'block';
                }
            } else {
                window.location.href = 'success.html';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            emailError.textContent = "Une erreur est survenue, veuillez réessayer.";
            emailError.style.display = 'block';
        });
    });

    // Fonction de validation de mot de passe
    function validatePassword(password) {
        const minLength = 8;
        const uppercaseRegex = /[A-Z]/;
        const digitRegex = /\d.*\d/;
        const specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/;

        return (
            password.length >= minLength &&
            uppercaseRegex.test(password) &&
            digitRegex.test(password) &&
            specialCharRegex.test(password)
        );
    }

    // Connexion avec le formulaire
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Empêche le rechargement de la page

        const form = e.target;
        const data = new FormData(form);

        fetch('login.php', {
            method: 'POST',
            body: data
        })
        .then(res => res.json()) // S'assurer que la réponse est au format JSON
        .then(response => {
            if (response.success) {
                window.location.href = response.redirect;
            } else {
                // Afficher l'erreur en dessous du mot de passe (si le mot de passe est incorrect par exemple)
                loginError.textContent = response.message;
                loginError.style.display = 'block';
                loginError.style.color = 'red'; // Message en rouge
            }
        })
        .catch(err => {
            // Afficher un message d'erreur générique
            loginError.textContent = "Une erreur est survenue. Veuillez réessayer.";
            loginError.style.display = 'block';
            loginError.style.color = 'red'; // Message en rouge
        });
    });
});
