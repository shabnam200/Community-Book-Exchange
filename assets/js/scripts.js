function validateRegistrationForm(event) {
    const form = event.target;
    const email = form.querySelector('input[name="email"]').value;
    const password = form.querySelector('input[name="password"]').value;
    const fullname = form.querySelector('input[name="fullname"]').value;
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (fullname.trim() === "") {
        alert("Full Name is required.");
        event.preventDefault();
        return;
    }

    if (!emailRegex.test(email)) {
        alert("Please enter a valid email address.");
        event.preventDefault();
        return;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters long.");
        event.preventDefault();
        return;
    }
}

function validateAddBookForm(event) {
    const form = event.target;
    const title = form.querySelector('input[name="title"]').value; 
    const author = form.querySelector('input[name="author"]').value; 
    const fileInput = form.querySelector('input[name="cover_image"]');
    
    if (title.trim() === "" || author.trim() === "") {
        alert("Book Title and Author are required fields.");
        event.preventDefault();
        return;
    }

    if (!fileInput || fileInput.files.length === 0) {
        alert("Please upload a book cover image.");
        event.preventDefault();
        return;
    }
}


document.addEventListener('DOMContentLoaded', () => {
    
    const registerForm = document.querySelector('.register-box form');
    if (registerForm) {
        registerForm.addEventListener('submit', validateRegistrationForm);
    }

    
    const addBookForm = document.querySelector('.book-form');
    if (addBookForm) {
        addBookForm.addEventListener('submit', validateAddBookForm);
    }
});