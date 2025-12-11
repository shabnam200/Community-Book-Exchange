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
        alert("A book cover image is required.");
        event.preventDefault();
        return;
    }

    const file = fileInput.files[0];
    const maxFileSize = 5 * 1024 * 1024; 

    if (file.size > maxFileSize) {
        alert("The image file size must not exceed 5MB.");
        event.preventDefault();
        return;
    }
}



document.addEventListener('DOMContentLoaded', function() {
    
    
    const regForm = document.getElementById('registration-form');
    if (regForm) {
        regForm.addEventListener('submit', validateRegistrationForm);
    }
    
    const addBookForm = document.querySelector('.book-form');
    if (addBookForm) {
        addBookForm.addEventListener('submit', validateAddBookForm);
    }

    
    const actionForms = document.querySelectorAll('.request-action-form');
    actionForms.forEach(form => {
        
        form.addEventListener('click', function(e) {
            const button = e.target.closest('button[data-action]');
            if (!button) return; 

            e.preventDefault();
            
            const action = button.getAttribute('data-action');
            const requestId = form.querySelector('input[name="request_id"]').value;
            
            if (!confirm(`Are you sure you want to ${action} this request?`)) {
                return;
            }

            const formData = new FormData();
            formData.append('request_id', requestId);
            formData.append('action', action);

            
            const allButtons = form.querySelectorAll('button');
            allButtons.forEach(btn => btn.disabled = true);
            button.textContent = 'Processing...';

            fetch('php/handle_request.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(`Request ${action}ed successfully! The Requester's email is now available on the page.`);
                    
                    window.location.reload(); 
                } else {
                    alert('Error processing request: ' + (data.error || 'Unknown error.'));
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                alert('An unexpected error occurred while processing the request.');
                
                allButtons.forEach(btn => btn.disabled = false);
                button.textContent = action.charAt(0).toUpperCase() + action.slice(1);
            });
        });
    });
});