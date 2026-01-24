@extends('layouts.admin')

@section('content')
<style>
    .app-notification-wrapper {
        padding: 5rem 0px;
        font-family: "Saira", Arial, Helvetica, sans-serif;
    }

    .card-title {
        color: #614092;
    }

    .loader {
        width: 20px;
        height: 20px;
        border: 3px solid #ffffff;
        border-bottom-color: transparent;
        border-radius: 50%;
        display: inline-block;
        box-sizing: border-box;
        animation: rotation 1s linear infinite;
        margin-right: 8px;
        vertical-align: middle;
    }

    @keyframes rotation {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .btn-success.sending {
        opacity: 0.8;
        cursor: not-allowed;
    }

    .form-control.success {
        animation: successPulse 0.5s ease-in-out;
    }

    @keyframes successPulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.02);
            border-color: #198754;
        }
        100% {
            transform: scale(1);
        }
    }
</style>

<div class="container-fluid app-notification-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-purple text-white">
                    <h4 class="card-title mb-0">App Notification Management</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form id="notificationForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Notification Title</label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Notification Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="10" 
                                      required></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="button" id="sendDirectBtn" class="btn btn-success">
                                <span class="button-content">Send Notification</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }, 3000);
        }
    
        const sendDirectBtn = document.getElementById('sendDirectBtn');
        const titleInput = document.getElementById('title');
        const descriptionInput = document.getElementById('description');
        const buttonContent = sendDirectBtn.querySelector('.button-content');
        
        sendDirectBtn.addEventListener('click', async function() {
            const title = titleInput.value;
            const description = descriptionInput.value;
    
            if (!title || !description) {
                showAlert('Please fill in both title and description', 'danger');
                return;
            }
    
            sendDirectBtn.disabled = true;
            sendDirectBtn.classList.add('sending');
            buttonContent.innerHTML = '<span class="loader"></span>Sending...';
    
            try {
                const response = await fetch('/admin/app-notification-content/send-direct', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        title: title,
                        description: description
                    })
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    showAlert(data.message, 'success');
                    
                    titleInput.classList.add('success');
                    descriptionInput.classList.add('success');
                    
                    setTimeout(() => {
                        titleInput.value = '';
                        descriptionInput.value = '';
                        
                        setTimeout(() => {
                            titleInput.classList.remove('success');
                            descriptionInput.classList.remove('success');
                        }, 500);
                    }, 300);
                } else {
                    showAlert(data.message || 'Failed to send notification', 'danger');
                }
            } catch (error) {
                showAlert('Error sending notification', 'danger');
                console.error('Error:', error);
            } finally {
                sendDirectBtn.disabled = false;
                sendDirectBtn.classList.remove('sending');
                buttonContent.textContent = 'Send Notification';
            }
        });
    
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const cardBody = document.querySelector('.card-body');
            cardBody.insertBefore(alertDiv, cardBody.firstChild);
    
            setTimeout(function() {
                alertDiv.style.opacity = '0';
                alertDiv.style.transition = 'opacity 0.5s';
                setTimeout(function() {
                    alertDiv.remove();
                }, 500);
            }, 3000);
        }
    });
</script>
@endsection