@extends('layouts.login')
@section('content')
<style>
  .file-size-error {
        font-size: 14px !important;
        font-weight: 500;
        color: #dc3545 !important;
        padding: 5px 0;
    }
  .upload-btn-wrapper {
        position: relative;
        margin-bottom: 20px;
    }
  .upload-btn-wrapper .error-container {
        position: relative !important;
        margin-top: 5px;
    }
   .forms-wrapper{
        flex-direction: column;
    }
    .error-container,#company-logo-error {
        color: red;
        font-size: 10px;
        position: absolute;
        bottom: 0;
        left: 0;
    }
  
    .error-msg{
       color: red;
        font-size: 10px;
       
  }
    .float-label-form-group{
        padding-bottom: 20px;
    }
    .required-star {
        color: red;
        margin-left: 3px;
    }
    .float-label-form-group label {
        display: flex;
        align-items: center;
        gap: 2px;
    }
    /* Rest of your existing styles remain the same */
    .forms-wrapper {
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #888 #F1F1F1;
    }
    .forms-wrapper::-webkit-scrollbar {
        width: 8px;
    }
    .forms-wrapper::-webkit-scrollbar-track {
        background: #F1F1F1;
    }
    .forms-wrapper::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 10px;
        border: 2px solid #F1F1F1;
    }
    .forms-wrapper::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    .spinner {
        display: none;
        width: 20px;
        height: 20px;
        margin-left: 10px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background-color: #4CAF50;
        color: white;
        border-radius: 5px;
        display: none;
        z-index: 1000;
        animation: slideIn 0.5s ease-in-out;
    }
    
    @keyframes slideIn {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }
    @media (max-width: 640px) {
        .forms-wrapper{
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding: 20px !important;
            position: relative;
            height: 100%;
            max-height: calc(100vh - 100px);
            padding-bottom: 50px !important;
        }
    }
</style>
<section class="registeration-wrapper">
    <div class="background-bg">
        <img src="{{ asset('uploads/images/register-bg.jpg') }}" />
    </div>
    <div class="logo">
        <a href="{{route('index')}}" target="_blank"><img src="{{ asset('uploads/images/logo.png') }}" /></a>
    </div>
    <div class="forms-wrapper d-flex align-items-center">
        <div class="inner">
            <h3 class="text-center">PBSF Registration Form</h3>
            <form id="team_form" action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="form-general-error" class="alert alert-danger d-none"></div>
                
                <div class="float-label-form-group">
                    <input type="text" name="name" id="rf-ur-team-name" class="form-control" placeholder="Enter your Team Name" value="{{ old('name') }}" required>
                    <label for="rf-ur-team-name">Enter your Team Name<span class="required-star">*</span></label>
                    <div class="error-container"></div>
                </div>

                <div class="float-label-form-group">
                    <input type="email" name="email" id="rf-ur-official-email" class="form-control" placeholder="Enter your Official Email" value="{{ old('email') }}" required>
                    <label for="rf-ur-official-email">Enter your Official Email<span class="required-star">*</span></label>
                    <div class="error-container"></div>
                </div>

                <div class="float-label-form-group">
                    <input type="text" name="phone" id="rf-ur-phone-number" class="form-control" placeholder="Phone Number" value="{{ old('phone') }}" required>
                    <label for="rf-ur-phone-number">Phone Number<span class="required-star">*</span></label>
                    <div class="error-container"></div>
                </div>

                <div class="upload-btn-wrapper">
                    <button class="btn">Upload Company Logo <img class="upload-icon" width="25" height="25" src="{{ asset('uploads/images/upload.png') }}" /></button>
                    <input type="file" name="logo" id="company-logo" accept=".png, .jpg, .jpeg, .svg" />
                    <div class="error-container text-end w-100"></div>
                    <div id="logo-filename" class="file-name-container"></div>
                </div>

                <div class="float-label-form-group">
                    <input type="password" name="password" id="rf-ur-password" class="form-control" placeholder="Password" required>
                    <label for="rf-ur-password">Password<span class="required-star">*</span></label>
                    <div class="error-container"></div>
                </div>

                <div class="float-label-form-group">
                    <input type="password" name="password_confirmation" id="rf-ur-confirm-password" class="form-control" placeholder="Confirm Password" required>
                    <label for="rf-ur-confirm-password">Confirm Password<span class="required-star">*</span></label>
                    <div class="error-container"></div>
                </div>
              
                  <div class="float-label-form-group mb-4" style="margin-top: 19px;">
                        <div class="g-recaptcha" data-callback="recaptchaCallback" data-sitekey="6Ld8cacqAAAAANMe-wSiV9rKi9qtNx9xCDjdi4nE" required></div>  
                    </div>
                 <div class="error-msg" id="captchaError" style="margin-top: -33px;"></div>

                <div class="pwd-note">
                    <h6>Passwords should contain each of the following character types:</h6>
                    <p class="m-0">[A-Z,a-z,0-9,~`!@#$%^&*()_-+={[}]|\:;"'<,>.?/]</p>
                </div>

                <button class="btn submitbtn" type="button" id="register-button">
                    <span>Submit</span>
                    <div class="spinner"></div>
                </button>

                <p class="text-center my-4"><small>Already signed up? <a href="{{ route('login') }}">Login Now</a></small></p>
            </form>
        </div>
        <div id="successToast" class="toast">
            Registration Successful!
        </div>
    </div>
</section>

<script>

// Updated file input validation with larger file size error
document.getElementById('company-logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const fileNameContainer = document.getElementById('logo-filename');
    const errorContainer = document.querySelector('.upload-btn-wrapper .error-container');
    
    // Clear previous error and filename
    errorContainer.textContent = '';
    fileNameContainer.textContent = '';
    errorContainer.classList.remove('file-size-error'); // Remove custom class if exists
    
    if (file) {
        const maxSize = 1 * 1024 * 1024; // 1MB in bytes
        if (file.size > maxSize) {
            errorContainer.classList.add('file-size-error'); // Add custom class for file size error
            errorContainer.textContent = 'File size must not exceed 1MB';
            this.value = ''; // Clear the file input
            
            // Automatically hide the error message after 2 seconds
            setTimeout(() => {
                // Fade out effect
                errorContainer.style.transition = 'opacity 0.5s ease-out';
                errorContainer.style.opacity = '0';
                
                // Clear the message and reset opacity after fade out
                setTimeout(() => {
                    errorContainer.textContent = '';
                    errorContainer.style.opacity = '1';
                    errorContainer.style.transition = '';
                    errorContainer.classList.remove('file-size-error'); // Remove custom class
                }, 500);
            }, 4000);
            
            return false;
        }
        
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/svg+xml'];
        if (!allowedTypes.includes(file.type)) {
            errorContainer.textContent = 'Only JPG, PNG, and SVG files are allowed';
            this.value = '';
            
            // Automatically hide the error message after 2 seconds
            setTimeout(() => {
                errorContainer.style.transition = 'opacity 0.5s ease-out';
                errorContainer.style.opacity = '0';
                
                setTimeout(() => {
                    errorContainer.textContent = '';
                    errorContainer.style.opacity = '1';
                    errorContainer.style.transition = '';
                }, 500);
            }, 2000);
            
            return false;
        }
        
        fileNameContainer.textContent = file.name;
    }
});

  let recaptchaCompleted = false;
  
  function recaptchaCallback() {
    recaptchaCompleted = true;
    document.getElementById('captchaError').textContent = '';
}
  
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('team_form');
    const inputs = form.querySelectorAll('input[required]');
    
    // Add validation for each required input
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(this);
        });
        
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
    
    // Validate password matching
    const passwordConfirm = document.getElementById('rf-ur-confirm-password');
    passwordConfirm.addEventListener('input', function() {
        const password = document.getElementById('rf-ur-password').value;
        const errorContainer = this.closest('.float-label-form-group').querySelector('.error-container');
        
        if (this.value && this.value !== password) {
            errorContainer.textContent = 'Passwords do not match';
        } else {
            errorContainer.textContent = '';
        }
    });
});

// Field validation function
function validateField(field) {
    const errorContainer = field.closest('.float-label-form-group').querySelector('.error-container');
    
    if (!field.value.trim()) {
        errorContainer.textContent = 'This field is required';
        return false;
    }
    
    // Email validation
    if (field.type === 'email') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(field.value)) {
            errorContainer.textContent = 'Please enter a valid email address';
            return false;
        }
    }
    
    // Phone validation
    if (field.name === 'phone') {
        const phoneRegex = /^\d{10}$/;
        if (!phoneRegex.test(field.value)) {
            errorContainer.textContent = 'Please enter a valid 10-digit phone number';
            return false;
        }
    }
    
    // Password validation
    if (field.name === 'password') {
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[~`!@#$%^&*()_\-+={[}\]|:;"'<,>.?/]).{8,}$/;
        if (!passwordRegex.test(field.value)) {
            errorContainer.textContent = 'Password must include uppercase, lowercase, number, and special character';
            return false;
        }
    }
    
    errorContainer.textContent = '';
    return true;
}

// Form submission
document.getElementById('register-button').addEventListener('click', function() {
    const form = document.getElementById('team_form');
    const formData = new FormData(form);
    const generalError = document.getElementById('form-general-error');
    const button = this;
    const buttonText = button.querySelector('span');
    const spinner = button.querySelector('.spinner');
    let hasErrors = false;

   if (!recaptchaCompleted) {
        document.getElementById('captchaError').textContent = 'Please complete the captcha';
        hasErrors = true;
    }
  
    // Validate all required fields
    const inputs = form.querySelectorAll('input[required]');
    inputs.forEach(input => {
        if (!validateField(input)) {
            hasErrors = true;
        }
    });

    // Validate file size if a file is selected
    const fileInput = document.getElementById('company-logo');
    const fileErrorContainer = document.querySelector('.upload-btn-wrapper .error-container');
    
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        if (file.size > 1 * 1024 * 1024) {
            fileErrorContainer.textContent = 'File size must not exceed 1MB';
            hasErrors = true;
        }
    }

    if (hasErrors) {
        generalError.textContent = 'Please fill all required fields correctly';
        generalError.classList.remove('d-none');
        return;
    }

   formData.append('g-recaptcha-response', grecaptcha.getResponse());
    // Proceed with form submission
    button.disabled = true;
    buttonText.style.opacity = '0.7';
    spinner.style.display = 'inline-block';
    generalError.classList.add('d-none');

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(response => {
        if (response.status === 422) {
            return response.json().then(data => {
                spinner.style.display = 'none';
                button.disabled = false;
                buttonText.style.opacity = '1';
                
                const errors = data.errors;
                for (const field in errors) {
                    const errorElement = document.querySelector(`[name="${field}"]`)?.closest('.float-label-form-group')?.querySelector('.error-container');
                    if (errorElement) {
                        errorElement.textContent = errors[field][0];
                    }
                }
              
               grecaptcha.reset();
               recaptchaCompleted = false;
              
                generalError.textContent = 'Please correct the errors below';
                generalError.classList.remove('d-none');
            });
        } else if (response.status === 200) {
            return response.json().then(data => {
                if (data.success) {
                    const toast = document.getElementById('successToast');
                    toast.style.display = 'block';
                    
                    setTimeout(() => {
                        toast.style.display = 'none';
                        window.location.href = data.redirect_url;
                    }, 3000);
                }
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        spinner.style.display = 'none';
        button.disabled = false;
        buttonText.style.opacity = '1';
      recaptcha.reset();
        recaptchaCompleted = false;
        generalError.textContent = 'An unexpected error occurred. Please try again.';
        generalError.classList.remove('d-none');
    });
});
</script>
@endsection