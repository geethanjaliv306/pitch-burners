@extends('layouts.login')
@section('content')
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<style>
    .error-container {
        color: red;
        font-size: 10px;
        position: absolute;
        bottom: 0;
        left: 0;
    }
    .float-label-form-group{
        padding-bottom: 20px;
    }
    .spinner-border {
        width: 1.5rem;
        height: 1.5rem;
        border-width: 0.25em;
        display: none;
    }
    .btn-text {
        display: inline-block;
    }
    .btn-loading {
        display: none;
    }

    #forgotPasswordModal .modal-dialog {
        margin: 0 auto;
        max-width: 450px;
        width: 90%;
    }

    #forgotPasswordModal .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        max-width: 400px;
        margin: auto;
        background: white;
    }

    #forgotPasswordModal .modal-header {
        padding: 1.5rem;
        background: linear-gradient(to right, #f0f7ff, #e6f0ff);
        border: none;
        border-radius: 1rem 1rem 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    #forgotPasswordModal .modal-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a365d;
        margin: 0 auto;
    }

    #forgotPasswordModal .btn-close {
        position: absolute;
        right: 1.5rem;
        background: none;
        border: none;
        padding: 0;
    }

    #forgotPasswordModal .btn-close:hover {
        opacity: 0.7;
    }

    #forgotPasswordModal .modal-body {
        padding: 16px;
        text-align: center;
    }

    #forgotPasswordModal .message-text {
        color: #666;
        margin-bottom: 20px;
        font-size: 15px;
    }

    #forgotPasswordModal .contact-card {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 10px;
    }

    #forgotPasswordModal .contact-card a {
        margin-left: 15px;
        color: #2563eb;
        text-decoration: none;
        font-size: 14px;
    }
    #forgotPasswordModal .contact-card a:hover {
        text-decoration: underline;
    }

    @media (max-width: 480px) {
        #forgotPasswordModal .modal-dialog {
            margin: 10px;
            width: auto;
        }
    }
    .float-label-form-group {
    position: relative;
}

#toggle-password i {
    font-size: 18px;
    color: #ccc;

}
    .modal-step {
        display: none;
    }
    
    .modal-step.active {
        display: block;
    }
    
    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }
    
    .step-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #e2e8f0;
        margin: 0 5px;
    }
    
    .step-dot.active {
        background: #2563eb;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-control {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0.75rem;
        width: 100%;
    }
    
    .btn-primary {
        background: #2563eb;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        width: 100%;
        margin-top: 1rem;
    }
    
    .btn-primary:disabled {
        background: #93c5fd;
        cursor: not-allowed;
    }
    
    .resend-otp {
        color: #2563eb;
        text-decoration: none;
        font-size: 14px;
        margin-top: 10px;
        display: inline-block;
    }

    /* Add or update these styles */
    .modal-step .spinner-border-sm {
        display: none;
        width: 1rem;
        height: 1rem;
        margin-left: 8px;
        vertical-align: middle;
    }

    .modal-step .btn-text {
        display: inline-block;
    }
</style>
@if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first('message', 'Please fill in all required fields.') }}
    </div>
@endif

    <section class="registeration-wrapper">
        <div class="background-bg">
            <img src="{{ asset('uploads/images/register-bg.jpg') }}" />
        </div>
        <div class="logo">
            <a href="{{route('index')}}" target="_blank"><img src="{{ asset('uploads/images/logo.png') }}" /></a>
        </div>
        <div class="forms-wrapper d-flex align-items-center">
            <div class="inner has-login">
                <h3 class="text-center">Login</h3>
                <form id="login-form" method="POST" action="{{ route('login') }}" data-ajax="true">
                    @csrf
                    <div id="error-message" class="alert alert-danger d-none"></div>
                    <div id="success-message" class="alert alert-success d-none">Login successful. Redirecting...</div>
                    <div class="float-label-form-group">
                        <input type="email" name="email" id="rf-ur-official-email" class="form-control" placeholder="Enter your Official Email">
                        <label for="rf-ur-official-email">Enter your Official Email</label>
                    </div>
                    <div class="float-label-form-group">
                        <input type="password" name="password" id="rf-ur-password" class="form-control" placeholder="Password">
                        <label for="rf-ur-password">Password</label>
                       <span id="toggle-password" style="cursor: pointer; position: absolute; right: 10px; top: 47%; transform: translateY(-50%);">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </span>
                    </div>
                    <button class="btn submitbtn" type="button" id="login-button">
                        <span class="btn-text">Submit</span>
                        <span class="spinner-border btn-loading" role="status" aria-hidden="true"></span>
                    </button>
                    <div class="d-flex justify-content-center align-items-center gap-4">
                        <div class="text-center">
                            <a href="#" id="forgot-password-link">Forgot Password?</a>
                            {{--  <a href="{{ route('forget_password')}}" class="login-pwd-txt font-size-18" onclick="forgetPwd()">Forgot password?</a>  --}}
                        </div>
                        <p class="text-center mb-0"><small>Not signed up? <a href="{{ route('register')}}">Register Now</a></small></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <div class="modal fade" id="forgotPasswordModal2" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 400px; margin: auto;">
                <div class="modal-header" style="border: none; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <h5 class="modal-title" style="font-size: 20px; color: #1a1a1a; font-weight: 600;">Reset Your Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; padding: 0;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M18 6L6 18M6 6L18 18" stroke="#1a1a1a" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
                <div class="modal-body" style="padding: 16px; text-align: center;">                  
                    <p style="color: #666; margin-bottom: 20px;font-size:15px">Please contact our admin team to reset your password</p>
                    
                    <!-- Contact Information -->
                    <div style="display: flex; align-items: center; margin-bottom: 15px; padding: 12px; background: #f8f9fa; border-radius: 10px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <a href="mailto:pitchburners@gmail.com" style="margin-left: 15px; color: #2563eb; text-decoration: none; font-size: 14px;">pitchburners@gmail.com</a>
                    </div>
                    
                    <div style="display: flex; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 10px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                            <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <a href="tel:990090908987" style="margin-left: 15px; color: #2563eb; text-decoration: none; font-size: 14px;">9840217047</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="forgotPasswordModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Your Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="step-indicator">
                        <div class="step-dot active"></div>
                        <div class="step-dot"></div>
                        <div class="step-dot"></div>
                    </div>
                    
                    <div class="modal-step active" id="step-email">
                        <form id="email-form">
                            <div class="form-group">
                                <label class='text-start w-100 mb-2'>Enter your email address</label>
                                <input type="email" class="form-control" id="reset-email" >
                            </div>
                            <div class="success-message d-none mb-3 text-success">
                                <small>OTP has been sent to your email successfully!</small>
                            </div>
                            <div class="error-message d-none mb-3 text-danger">
                                <small></small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <span class="btn-text">Send OTP</span>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>
                        </form>
                    </div>
                    
                    <div class="modal-step" id="step-otp">
                        <form id="otp-form">
                            <div class="form-group">
                                <label class="text-start w-100 mb-2">Enter OTP sent to your email</label>
                                <input type="text" class="form-control" id="otp-input" maxlength="6" >
                            </div>
                            <div class="success-message d-none mb-3 text-success">
                                <small>OTP verified successfully!</small>
                            </div>
                            <div class="error-message d-none mb-3 text-danger">
                                <small></small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <span class="btn-text">Verify OTP</span>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>                            
                            <div class="text-center">
                                <div class="resend-message d-none mt-2">
                                    <small class="text-success">OTP resent successfully!</small>
                                </div>
                                <a href="#" class="resend-otp">Resend OTP</a>
                            </div>
                        </form>
                    </div>
                    
                    <div class="modal-step" id="step-password">
                        <form id="password-form">
                            <div class="form-group">
                                <label class="text-start w-100 mb-2">New Password</label>
                                <input type="password" class="form-control" id="new-password" >
                            </div>
                            <div class="form-group">
                                <label class="text-start w-100 mb-2">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm-password" >
                            </div>
                            <div class="success-message d-none mb-3 text-success">
                                <small>Password reset successful! Redirecting to login...</small>
                            </div>
                            <div class="error-message d-none mb-3 text-danger">
                                <small></small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <span class="btn-text">Reset Password</span>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
      
      document.getElementById('login-form').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('login-button').click();
            }
        });

        const emailForm = document.getElementById('email-form');
        const otpForm = document.getElementById('otp-form');
        const passwordForm = document.getElementById('password-form');

        emailForm.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                emailForm.querySelector('button[type="submit"]').click();
            }
        });

        otpForm.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                otpForm.querySelector('button[type="submit"]').click();
            }
        });

        passwordForm.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                passwordForm.querySelector('button[type="submit"]').click();
            }
        });

      
        document.getElementById('login-button').addEventListener('click', function () {
            const email = document.getElementById('rf-ur-official-email').value.trim();
            const password = document.getElementById('rf-ur-password').value.trim();
            const form = document.getElementById('login-form');
            const spinner = document.querySelector('.spinner-border');
            const buttonText = document.querySelector('.btn-text');
            const successMessage = document.getElementById('success-message');
            const errorDiv = document.getElementById('error-message');

            if (!email || !password) {
                errorDiv.textContent = 'Both email and password are required.';
                errorDiv.classList.remove('d-none');
                setTimeout(() => {
                    errorDiv.classList.add('d-none');
                }, 2000);
                return;
            }

            spinner.style.display = 'inline-block';
            buttonText.style.display = 'none';
            successMessage.classList.add('d-none');

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                spinner.style.display = 'none';
                buttonText.style.display = 'inline-block';
                if (data.success) {
                    successMessage.classList.remove('d-none');
                    setTimeout(function () {
                        window.location.href = data.redirect_url;
                    }, 2000);
                } else {
                    errorDiv.textContent = data.message || 'An error occurred. Please try again.';
                    errorDiv.classList.remove('d-none');
                    setTimeout(() => {
                        errorDiv.classList.add('d-none');
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                spinner.style.display = 'none';
                buttonText.style.display = 'inline-block';
            });
        });

        const forgotPasswordModal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
        document.getElementById('forgot-password-link').addEventListener('click', function(e) {
            e.preventDefault();
            forgotPasswordModal.show();
        });
    });
document.getElementById('toggle-password').addEventListener('click', function () {
    const passwordInput = document.getElementById('rf-ur-password');
    const icon = this.querySelector('i');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('forgotPasswordModal');
        const steps = ['step-email', 'step-otp', 'step-password'];
        let currentStep = 0;
        let userEmail = '';
        
        function showMessage(step, type, message) {
            const stepElement = document.getElementById(step);
            const successMsg = stepElement.querySelector('.success-message');
            const errorMsg = stepElement.querySelector('.error-message');
            const spinner = stepElement.querySelector('.spinner-border-sm');
            const btnText = stepElement.querySelector('.btn-text');
            
            successMsg.classList.add('d-none');
            errorMsg.classList.add('d-none');
            
            if (type === 'success') {
                successMsg.querySelector('small').textContent = message;
                successMsg.classList.remove('d-none');
            } else {
                errorMsg.querySelector('small').textContent = message;
                errorMsg.classList.remove('d-none');
            }
            
            if (spinner && btnText) {
                spinner.style.display = 'none';
                btnText.style.display = 'inline-block';
                btnText.style.marginRight = '0';
            }
        }
        
        function showLoading(form) {
            const spinner = form.querySelector('.spinner-border-sm');
            const btnText = form.querySelector('.btn-text');
            if (spinner && btnText) {
                spinner.style.display = 'inline-block';
                btnText.style.display = 'inline-block';
                btnText.style.marginRight = '8px';
            }
        }
    
        function hideLoading(form) {
            const spinner = form.querySelector('.spinner-border-sm');
            const btnText = form.querySelector('.btn-text');
            if (spinner && btnText) {
                spinner.style.display = 'none';
                btnText.style.display = 'inline-block';
                btnText.style.marginRight = '0';
            }
        }
    
        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
        
        document.getElementById('email-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('reset-email').value.trim();
            
            if (!email) {
                showMessage('step-email', 'error', 'Please enter your email address');
                return;
            }
            
            if (!isValidEmail(email)) {
                showMessage('step-email', 'error', 'Please enter a valid email address');
                return;
            }
            
            showLoading(this);
            userEmail = email;
            $.ajax({
                url: '{{ route("send.otp") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    email: email
                },
                success: function(response) {
                    showMessage('step-email', 'success', 'OTP sent successfully!');
                    setTimeout(() => goToNextStep(), 1000);
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors && xhr.responseJSON.errors.email) {
                            errorMessage = xhr.responseJSON.errors.email[0];
                        } else if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                    }
                    showMessage('step-email', 'error', errorMessage);
                    hideLoading(this);
                }
            });
        });
        
        document.getElementById('otp-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const otp = document.getElementById('otp-input').value.trim();
            
            if (!otp) {
                showMessage('step-otp', 'error', 'Please enter the OTP');
                return;
            }
            
            if (otp.length !== 6 || !/^\d+$/.test(otp)) {
                showMessage('step-otp', 'error', 'Please enter a valid 6-digit OTP');
                return;
            }
            
            showLoading(this);

            $.ajax({
                url: '{{ route("verify.otp") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    email:userEmail,
                    otp: otp
                },
                success: function(response) {
                    showMessage('step-otp', 'success', 'OTP verified successfully!');
                    setTimeout(() => goToNextStep(), 1000);
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Invalid OTP. Please try again.';
                    showMessage('step-otp', 'error', 'Invalid OTP. Please try again.');
                    hideLoading(this);
                }
            });
        });
        
        document.getElementById('password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            if (!newPassword) {
                showMessage('step-password', 'error', 'Please enter a new password');
                return;
            }
            
            if (newPassword.length < 3) {
                showMessage('step-password', 'error', 'Password must be at least 8 characters long');
                return;
            }
            
            if (!confirmPassword) {
                showMessage('step-password', 'error', 'Please confirm your new password');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                showMessage('step-password', 'error', 'Passwords do not match');
                return;
            }
            
            showLoading(this);

            $.ajax({
                url: '{{ route("reset.password") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    email: userEmail,
                    new_password: newPassword,
                    confirm_password: confirmPassword
                },
                success: function(response) {
                    showMessage('step-password', 'success', 'Password reset successful! Redirecting to login...');

                    setTimeout(() => {
                        const bootstrapModal = bootstrap.Modal.getInstance(modal);
                        bootstrapModal.hide();
                        resetModal();
                        setTimeout(() => window.location.reload(true), 500);
                    }, 2000);
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Failed to reset password. Please try again.';
                    showMessage('step-password', 'error', errorMessage);
                }
            });
        });
        
        document.querySelector('.resend-otp').addEventListener('click', function(e) {
            e.preventDefault();
            const resendMsg = document.querySelector('.resend-message');
            const resendLink = this;
            resendLink.style.opacity = '0.5';
            resendLink.style.pointerEvents = 'none';

            const loader = document.createElement('span');
            loader.className = 'spinner-border spinner-border-sm ms-2';
            loader.style.width = '1rem';
            loader.style.height = '1rem';
            resendLink.parentNode.appendChild(loader);
            
            resendMsg.classList.add('d-none');

            $.ajax({
                url: '{{ route("send.otp") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    email: userEmail
                },
                success: function(response) {
                    loader.remove();
                    resendLink.style.opacity = '1';
                    resendLink.style.pointerEvents = 'auto';
                    showMessage('step-otp', 'success', 'OTP resent successfully!', 3000);
                    
                    setTimeout(() => {
                        resendMsg.classList.add('d-none');
                    }, 2000);
                },
                error: function(xhr) {
                    loader.remove();
                    resendLink.style.opacity = '1';
                    resendLink.style.pointerEvents = 'auto';
                    const errorMessage = xhr.responseJSON?.message || 'Failed to resend OTP. Please try again.';
                    showMessage('step-otp', 'error', errorMessage);
                }
            });
        });
        
        function goToNextStep() {
            document.getElementById(steps[currentStep]).classList.remove('active');
            currentStep++;
            document.getElementById(steps[currentStep]).classList.add('active');
            
            const dots = document.querySelectorAll('.step-dot');
            dots.forEach((dot, index) => {
                if (index <= currentStep) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }
        
        function resetModal() {
            ['email-form', 'otp-form', 'password-form'].forEach(formId => {
                document.getElementById(formId).reset();
            });
            
            document.querySelectorAll('.success-message, .error-message').forEach(msg => {
                msg.classList.add('d-none');
            });
            
            document.querySelectorAll('.modal-step').forEach(step => {
                step.classList.remove('active');
            });
            document.getElementById('step-email').classList.add('active');
            
            document.querySelectorAll('.step-dot').forEach((dot, index) => {
                if (index === 0) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
            
            currentStep = 0;
            userEmail = ''; 
        }
    });
    function showLoading(form) {
        const spinner = form.querySelector('.spinner-border-sm');
        const btnText = form.querySelector('.btn-text');
        if (spinner && btnText) {
            spinner.style.display = 'inline-block';
            btnText.style.marginRight = '8px';
        }
    }
    
    function hideLoading(form) {
        const spinner = form.querySelector('.spinner-border-sm');
        const btnText = form.querySelector('.btn-text');
        if (spinner && btnText) {
            spinner.style.display = 'none';
            btnText.style.marginRight = '0';
        }
    }
    </script>
@endsection
