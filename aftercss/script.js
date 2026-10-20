

const closeNotification = document.getElementById("closeNotification");
const notification = document.getElementById("notification");

if (closeNotification && notification) {

    closeNotification.addEventListener("click", function () {

        notification.style.display = "none";

    });

}


// ==========================================
// 2. HAMBURGER MENU
// ==========================================

const menuButton = document.getElementById("menuButton");
const navLinks = document.getElementById("navLinks");

if (menuButton && navLinks) {

    menuButton.addEventListener("click", function () {

        navLinks.classList.toggle("show");

    });

}


// ==========================================
// 3. DARK / LIGHT MODE
// ==========================================

const themeButton = document.getElementById("themeButton");


// Check previously saved theme

const savedTheme = localStorage.getItem("studentHubTheme");

if (savedTheme === "dark") {

    document.body.classList.add("dark-mode");

    if (themeButton) {
        themeButton.textContent = "☀️";
    }

}


// Theme button

if (themeButton) {

    themeButton.addEventListener("click", function () {

        document.body.classList.toggle("dark-mode");


        if (document.body.classList.contains("dark-mode")) {

            localStorage.setItem(
                "studentHubTheme",
                "dark"
            );

            themeButton.textContent = "☀️";

        } else {

            localStorage.setItem(
                "studentHubTheme",
                "light"
            );

            themeButton.textContent = "🌙";

        }

    });

}


// ==========================================
// 4. MODAL POPUP
// ==========================================

const modal = document.getElementById("modal");
const closeModal = document.getElementById("closeModal");
const modalOk = document.getElementById("modalOk");

const modalTitle = document.getElementById("modalTitle");
const modalMessage = document.getElementById("modalMessage");

const openModalButtons =
    document.querySelectorAll(".open-modal");


// Open modal

openModalButtons.forEach(function (button) {

    button.addEventListener("click", function () {

        const title =
            button.getAttribute("data-title");

        const message =
            button.getAttribute("data-message");


        if (modalTitle) {
            modalTitle.textContent = title;
        }

        if (modalMessage) {
            modalMessage.textContent = message;
        }

        if (modal) {
            modal.classList.add("show");
        }

    });

});


// Function to close modal

function closePopup() {

    if (modal) {

        modal.classList.remove("show");

    }

}


// Close using X

if (closeModal) {

    closeModal.addEventListener(
        "click",
        closePopup
    );

}


// Close using OK

if (modalOk) {

    modalOk.addEventListener(
        "click",
        closePopup
    );

}


// Close by clicking outside modal

if (modal) {

    modal.addEventListener("click", function (event) {

        if (event.target === modal) {

            closePopup();

        }

    });

}


// Close using Escape key

document.addEventListener("keydown", function (event) {

    if (event.key === "Escape") {

        closePopup();

    }

});


// ==========================================
// 5. SLIDER
// ==========================================

const slides = [

    {
        title: "💻 Web Development",
        text: "Learn HTML, CSS and JavaScript."
    },

    {
        title: "☕ Java Programming",
        text: "Learn Java and object-oriented programming."
    },

    {
        title: "🌐 Computer Networks",
        text: "Learn networking, TCP/IP and routing."
    },

    {
        title: "📊 Data Structures",
        text: "Learn arrays, linked lists, stacks and queues."
    }

];


let currentSlide = 0;


const slideTitle =
    document.getElementById("slideTitle");

const slideText =
    document.getElementById("slideText");

const slideNumber =
    document.getElementById("slideNumber");

const nextSlide =
    document.getElementById("nextSlide");

const previousSlide =
    document.getElementById("previousSlide");


// Function to display slide

function displaySlide() {

    if (!slideTitle || !slideText) {
        return;
    }


    slideTitle.textContent =
        slides[currentSlide].title;

    slideText.textContent =
        slides[currentSlide].text;


    if (slideNumber) {

        slideNumber.textContent =
            (currentSlide + 1) +
            " / " +
            slides.length;

    }

}


// Next button

if (nextSlide) {

    nextSlide.addEventListener("click", function () {

        currentSlide++;

        if (currentSlide >= slides.length) {

            currentSlide = 0;

        }

        displaySlide();

    });

}


// Previous button

if (previousSlide) {

    previousSlide.addEventListener("click", function () {

        currentSlide--;

        if (currentSlide < 0) {

            currentSlide = slides.length - 1;

        }

        displaySlide();

    });

}


// Show first slide

displaySlide();


// ==========================================
// 6. FAQ
// ==========================================

const faqQuestions =
    document.querySelectorAll(".faq-question");


faqQuestions.forEach(function (question) {

    question.addEventListener("click", function () {

        const answer =
            question.nextElementSibling;


        answer.classList.toggle("active");


        const symbols =
            question.querySelectorAll("span");


        if (symbols.length > 1) {

            if (answer.classList.contains("active")) {

                symbols[1].textContent = "−";

            } else {

                symbols[1].textContent = "+";

            }

        }

    });

});


// 
// JAVASCRIPT LOADED
//

console.log("Student Hub JavaScript loaded successfully!");


/* PRACTICAL 5 - REGISTRATION FORM VALIDATION
   Does not interfere with other Student Hub pages */

(function () {
    const form = document.getElementById("registrationForm");

    // Run only on the registration page
    if (!form) {
        return;
    }

    function field(id) {
        return document.getElementById(id);
    }

    function showError(id, message) {
        const error = field(id + "Error");
        const input = field(id);

        if (error) {
            error.textContent = message;
        }

        if (input && input.type !== "radio" &&
            input.type !== "checkbox") {
            input.classList.add("invalid");
            input.classList.remove("valid");
            input.setAttribute("aria-invalid", "true");
        }
    }

    function clearError(id) {
        const error = field(id + "Error");
        const input = field(id);

        if (error) {
            error.textContent = "";
        }

        if (input && input.type !== "radio" &&
            input.type !== "checkbox") {
            input.classList.remove("invalid");
            input.classList.add("valid");
            input.removeAttribute("aria-invalid");
        }
    }

    function validateName() {
        const name = field("name").value.trim();
        const pattern = /^[A-Za-z][A-Za-z .'-]{1,48}[A-Za-z]$/;

        if (!name) {
            showError("name", "Please enter your full name.");
            return false;
        }

        if (!pattern.test(name)) {
            showError("name", "Enter a valid name with at least 3 characters.");
            return false;
        }

        clearError("name");
        return true;
    }

    function validateEmail() {
        const email = field("email").value.trim();
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!email) {
            showError("email", "Please enter your email address.");
            return false;
        }

        if (!pattern.test(email)) {
            showError("email", "Please enter a valid email address.");
            return false;
        }

        clearError("email");
        return true;
    }

    function validateMobile() {
        const mobile = field("mobile").value.trim();
        const pattern = /^[6-9]\d{9}$/;

        if (!mobile) {
            showError("mobile", "Please enter your mobile number.");
            return false;
        }

        if (!pattern.test(mobile)) {
            showError("mobile", "Enter a valid 10-digit Indian mobile number.");
            return false;
        }

        clearError("mobile");
        return true;
    }

    function checkPasswordStrength() {
        const password = field("password").value;
        const bar = field("strengthBar");
        const text = field("strengthText");

        let score = 0;

        if (password.length >= 8) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        const levels = [
            ["0%", "Not entered", "#999999"],
            ["20%", "Very weak", "#c62828"],
            ["40%", "Weak", "#e57373"],
            ["60%", "Fair", "#f0a500"],
            ["80%", "Good", "#66a83f"],
            ["100%", "Strong", "#218838"]
        ];

        bar.style.width = levels[score][0];
        bar.style.backgroundColor = levels[score][2];
        text.textContent = "Password strength: " + levels[score][1];
    }

    function validatePassword() {
        const password = field("password").value;
        const pattern =
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])\S{8,}$/;

        if (!password) {
            showError("password", "Please create a password.");
            return false;
        }

        if (!pattern.test(password)) {
            showError(
                "password",
                "Use 8+ characters with uppercase, lowercase, number and special character. No spaces."
            );
            return false;
        }

        clearError("password");
        return true;
    }

    function validateConfirmPassword() {
        const password = field("password").value;
        const confirm = field("confirmPassword").value;

        if (!confirm) {
            showError("confirmPassword", "Please confirm your password.");
            return false;
        }

        if (password !== confirm) {
            showError("confirmPassword", "Passwords do not match.");
            return false;
        }

        clearError("confirmPassword");
        return true;
    }

    function validateCourse() {
        if (!field("course").value) {
            showError("course", "Please select your course.");
            return false;
        }

        clearError("course");
        return true;
    }

    function validateYear() {
        if (!field("year").value) {
            showError("year", "Please select your academic year.");
            return false;
        }

        clearError("year");
        return true;
    }

    function validateGender() {
        const selected = form.querySelector(
            'input[name="gender"]:checked'
        );

        field("genderError").textContent = selected
            ? ""
            : "Please select your gender.";

        return Boolean(selected);
    }

    function validateTerms() {
        const terms = field("terms");

        if (!terms.checked) {
            field("termsError").textContent =
                "Please accept the terms and conditions.";
            terms.setAttribute("aria-invalid", "true");
            return false;
        }

        field("termsError").textContent = "";
        terms.removeAttribute("aria-invalid");
        return true;
    }

    // Real-time validation
    field("name").addEventListener("input", validateName);
    field("email").addEventListener("input", validateEmail);
    field("mobile").addEventListener("input", validateMobile);

    field("password").addEventListener("input", function () {
        checkPasswordStrength();
        validatePassword();

        if (field("confirmPassword").value) {
            validateConfirmPassword();
        }
    });

    field("confirmPassword").addEventListener(
        "input", validateConfirmPassword
    );

    field("course").addEventListener("change", validateCourse);
    field("year").addEventListener("change", validateYear);

    form.querySelectorAll('input[name="gender"]').forEach(function (radio) {
        radio.addEventListener("change", validateGender);
    });

    field("terms").addEventListener("change", validateTerms);

    // Submit validation
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const message = field("formMessage");
        message.textContent = "";
        message.className = "";

        const checks = [
            validateName(),
            validateEmail(),
            validateMobile(),
            validatePassword(),
            validateConfirmPassword(),
            validateCourse(),
            validateYear(),
            validateGender(),
            validateTerms()
        ];

        if (checks.every(Boolean)) {
            message.textContent =
                "Registration successful! All fields are valid.";
            message.className = "success-message";

            // Frontend demonstration only; no data is saved.
            form.reset();

            form.querySelectorAll(".valid, .invalid").forEach(function (el) {
                el.classList.remove("valid", "invalid");
                el.removeAttribute("aria-invalid");
            });

            field("strengthBar").style.width = "0%";
            field("strengthBar").style.backgroundColor = "#999999";
            field("strengthText").textContent =
                "Password strength: Not entered";
        } else {
            message.textContent =
                "Please correct the errors highlighted in the form.";
            message.className = "error-message";

            const firstInvalid = form.querySelector(
                '[aria-invalid="true"]'
            );

            if (firstInvalid) {
                firstInvalid.focus();
            }
        }
    });
})();