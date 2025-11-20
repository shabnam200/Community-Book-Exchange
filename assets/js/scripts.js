

function validateRegistrationForm(event) {

    event.preventDefault();

    const form = event.target;
    const email = form.querySelector('input[name="email"]').value;
    const password = form.querySelector('input[name="password"]').value;
    const fullname = form.querySelector('input[name="fullname"]').value;

    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (fullname.trim() === "") {
        alert("Full Name is required.");
        return;
    }

    if (!emailRegex.test(email)) {
        alert("Please enter a valid email address.");
        return;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters long.");
        return;
    }

    // If all checks pass, you would proceed with the form submission (to the backend)
    // For now, we simulate success and prevent submission for backend testing.
    alert("Registration data is valid. (Not submitting to server yet.)");
    // form.submit(); // Uncomment this line when backend is ready
}

function validateAddBookForm(event) {
    event.preventDefault();

    const form = event.target;
    const title = form.querySelector('input[placeholder="Enter book title"]').value;
    const author = form.querySelector('input[placeholder="Enter author name"]').value;
    const fileInput = form.querySelector('input[type="file"]');
    
    if (title.trim() === "" || author.trim() === "") {
        alert("Book Title and Author are required fields.");
        return;
    }

    if (fileInput.files.length === 0) {
        alert("Please upload a book cover image.");
        return;
    }

    
    alert("Book details are valid. (Not submitting to server yet.)");
    // form.submit(); // Uncomment this line when backend is ready
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