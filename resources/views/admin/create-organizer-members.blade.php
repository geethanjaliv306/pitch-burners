@extends('layouts.admin')

@section('content')
<style>
.startmatch-wrap {
    padding: 2rem 0;
}

.organizer-info {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}

.enter-members {
    margin-bottom: 2rem;
}

.membersSection {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
    position: relative;
}

.remove-section {
    margin-top: 1rem;
}

.is-invalid {
    border-color: #dc3545;
}

.error-message {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: none;
}

.is-invalid + .error-message {
    display: block;
}

.submit-btn {
    min-width: 120px;
}
</style>

<section class="startmatch-wrap dashboard-page">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="organizer-info">
                    <form method="POST" action="{{ route('organizer-members.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group enter-members p-0">
                            <label for="section-count">Enter the number of members:</label>
                            <input type="number" class="form-control" id="section-count" name="section_count" min="1" placeholder="Enter a number">
                        </div>
                        <div id="sections-container">
                            <div class="section membersSection mb-3" id="section-1">
                                <h5>Member 1</h5>
                                <div class="form-group">
                                    <label for="name-1">Name:</label>
                                    <input type="text" class="form-control" id="name-1" name="members[0][name]" placeholder="Enter name">
                                    <div class="error-message" id="name-error-1"></div>
                                </div>
                                <div class="form-group">
                                    <label for="email-1">Email:</label>
                                    <input type="email" class="form-control" id="email-1" name="members[0][email]" placeholder="Enter email">
                                    <div class="error-message" id="email-error-1"></div>
                                </div>
                                <div class="form-group">
                                    <label for="phone-1">Phone Number:</label>
                                    <input type="text" class="form-control" id="phone-1" name="members[0][phone_no]" placeholder="Enter phone number">
                                    <div class="error-message" id="phone-error-1"></div>
                                </div>
                                <div class="form-group">
                                    <label for="password-1">Password:</label>
                                    <input type="password" class="form-control" id="password-1" name="members[0][password]" placeholder="Enter password">
                                    <div class="error-message" id="password-error-1"></div>
                                </div>
                              <div class="form-group">
    <label for="image-1">Image:</label>
    <input type="file" class="form-control" name="members[0][image]" id="image-1" accept="image/*">
    <div class="error-message" id="image-error-1"></div>
</div>


                            </div>
                        </div>
                        <div class="col-12 pagination-wrap">
                            <button type="submit" class="btn btn-primary text-capitalize submit-btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

<script>
$(document).ready(function() {
    function createSection(index) {
        return `
            <div class="section mb-3 membersSection" id="section-${index}">
                <h5>Member ${index}</h5>
                <div class="form-group">
                    <label for="name-${index}">Name:</label>
                    <input type="text" class="form-control" id="name-${index}" name="members[${index - 1}][name]" placeholder="Enter name">
                    <div class="error-message" id="name-error-${index}"></div>
                </div>
                <div class="form-group">
                    <label for="email-${index}">Email:</label>
                    <input type="email" class="form-control" id="email-${index}" name="members[${index - 1}][email]" placeholder="Enter email">
                    <div class="error-message" id="email-error-${index}"></div>
                </div>
                <div class="form-group">
                    <label for="phone-${index}">Phone Number:</label>
                    <input type="text" class="form-control" id="phone-${index}" name="members[${index - 1}][phone_no]" placeholder="Enter phone number">
                    <div class="error-message" id="phone-error-${index}"></div>
                </div>
                <div class="form-group">
                    <label for="password-${index}">Password:</label>
                    <input type="password" class="form-control" id="password-${index}" name="members[${index - 1}][password]" placeholder="Enter Password">
                    <div class="error-message" id="password-error-${index}"></div>
                </div>
                <div class="form-group">
                    <label for="image-${index}">Image:</label>
                    <input type="file" class="form-control" name="members[${index - 1}][image]" id="image-${index}" accept="image/*">
                    <div class="error-message" id="image-error-${index}"></div>
                </div>
                <div class="remove-section"><button type="button" class="btn btn-danger" data-index="${index}">Delete Section</button></div>
            </div>
        `;
    }

    function clearErrors() {
        $('.form-control').removeClass('is-invalid');
        $('.error-message').text('').hide();
    }

    function showError(element, message) {
        element.addClass('is-invalid');
        element.siblings('.error-message').text(message).show();
    }

    function validateForm() {
        let isValid = true;
        clearErrors();

        $('.membersSection').each(function() {
            const section = $(this);
            const index = section.attr('id').split('-')[1];

            // Name validation
            const nameInput = section.find('input[id^="name"]');
            const name = nameInput.val();
            if (!name || name.trim().length < 2) {
                isValid = false;
                showError(nameInput, 'Please enter a valid name');
            }

            // Email validation
            const emailInput = section.find('input[id^="email"]');
            const email = emailInput.val();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                isValid = false;
                showError(emailInput, 'Please enter a valid email');
            }

            // Phone validation
            const phoneInput = section.find('input[id^="phone"]');
            const phone = phoneInput.val();
            const phoneRegex = /^\d{10}$/;
            if (!phone || !phoneRegex.test(phone)) {
                isValid = false;
                showError(phoneInput, 'Phone number must be 10 digits');
            }

            // Password validation
            const passwordInput = section.find('input[id^="password"]');
            const password = passwordInput.val();
            if (!password || password.length < 6) {
                isValid = false;
                showError(passwordInput, 'Please enter a valid password');
            }
           const imageInput = section.find('input[id^="image"]');
        const image = imageInput[0].files[0];
        if (!image) {
            isValid = false;
            showError(imageInput, 'Please upload an image');
        } else {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/svg'];
            if (!allowedTypes.includes(image.type)) {
                isValid = false;
                showError(imageInput, 'Only JPG, PNG, SVG  or JPEG images are allowed');
            }
        }
        });

        return isValid;
    }

    $('#section-count').on('input', function() {
        const count = parseInt($(this).val()) || 0;
        const currentSections = $('#sections-container .section').length;

        if (count > currentSections) {
            for (let i = currentSections + 1; i <= count; i++) {
                $('#sections-container').append(createSection(i));
            }
        } else {
            for (let i = currentSections; i > count; i--) {
                $(`#section-${i}`).remove();
            }
        }
    });

    $(document).on('click', '.remove-section button', function() {
        const index = $(this).data('index');
        $(`#section-${index}`).remove();

        $('#sections-container .section').each(function(i) {
            const newIndex = i + 1;
            $(this).attr('id', `section-${newIndex}`);
            $(this).find('h5').text(`Member ${newIndex}`);
            $(this).find('.remove-section button').data('index', newIndex);
            
            $(this).find('input, .error-message').each(function() {
                const oldName = $(this).attr('name');
                if (oldName) {
                    const newName = oldName.replace(/\d+/, newIndex - 1);
                    $(this).attr('name', newName);
                }
                
                const oldId = $(this).attr('id');
                if (oldId) {
                    const newId = oldId.replace(/\d+/, newIndex);
                    $(this).attr('id', newId);
                }
            });
        });

        $('#section-count').val($('#sections-container .section').length);
    });

    $(document).on('input', '.form-control', function() {
        $(this).removeClass('is-invalid');
        $(this).siblings('.error-message').text('').hide();
    });

    $('form').on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });

    $('#section-count').val(1);
});
</script>
@endsection