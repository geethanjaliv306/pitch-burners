$(document).ready(function() {
    var dropzone = $('#dropzone');
    var fileInput = $('#fileInput');
  
    dropzone.on('click', function() {
      fileInput.click();
    });
  
    fileInput.on('change', function(e) {
      handleFiles(e.target.files);
    });
  
    dropzone.on('dragover', function(e) {
      e.preventDefault();
      e.stopPropagation();
      dropzone.addClass('dragover');
    });
  
    dropzone.on('dragleave', function(e) {
      e.preventDefault();
      e.stopPropagation();
      dropzone.removeClass('dragover');
    });
  
    dropzone.on('drop', function(e) {
      e.preventDefault();
      e.stopPropagation();
      dropzone.removeClass('dragover');
      var files = e.originalEvent.dataTransfer.files;
      handleFiles(files);
    });
  
    function handleFiles(files) {
      if (files.length > 0) {
        var file = files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
          var img = $('<img>').attr('src', e.target.result);
          dropzone.html(img);
        };
        reader.readAsDataURL(file);
      }
    }
  });

$(document).ready(function(){
    $('#team_form').validate({
        rules: {
            name: {
                required: true,
                minlength: 3
            },
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 10
            },
            logo: {
                required: true,
                extension: "jpg|jpeg|png|svg"
            },
            password: {
                required: true,
                minlength: 8,
                pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[~`!@#$%^&*()_\-+={[}\]|:;"'<,>.?/]).+$/
            },
            password_confirmation: {
                required: true,
                equalTo: "#rf-ur-password"
            }
        },
        messages: {
            name: {
                required: "This field is required.",
                minlength: "Your name must be at least 3 characters long."
            },
            email: {
                required: "This field is required.",
                email: "Please enter a valid email address."
            },
            phone: {
                required: "This field is required.",
                digits: "Please enter a valid 10-digit phone number.",
                minlength: "Your phone number must be exactly 10 digits long.",
                maxlength: "Your phone number must be exactly 10 digits long."
            },
            logo: {
                required: "Please upload your company logo.",
                extension: "Allowed file types: jpg, jpeg, png, svg."
            },
            password: {
                required: "This field is required.",
                minlength: "Your password must be at least 8 characters long.",
                pattern: "Password must include uppercase, lowercase, number, and special character."
            },
            password_confirmation: {
                required: "This field is required.",
                equalTo: "Passwords do not match."
            }
        },
        errorElement: 'div',
        errorPlacement: function(error, element) {
            const errorContainer = element.closest('.float-label-form-group').find('.error-container');
            if (errorContainer.length) {
                error.appendTo(errorContainer);
            } else {
                error.insertAfter(element);
            }
        }
    });
    $('#login-form').validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
                minlength: 8,
                pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[~`!@#$%^&*()_\-+={[}\]|:;"'<,>.?/]).+$/
            }
        },
        messages: {
            email: {
                required: "This field is required.",
                email: "Please enter a valid email address."
            },
            password: {
                required: "This field is required.",
                minlength: "Your password must be at least 8 characters long.",
                pattern: "Password must include uppercase, lowercase, number, and special character."
            }
        },
        // Custom method for pattern validation
        errorElement: 'div',
        errorPlacement: function(error, element) {
            element.closest('.float-label-form-group').find('.error-container').append(error);
            error.appendTo(element.closest('.float-label-form-group').find('.error-container'));
        }
    });
    // Custom pattern method if needed
    $.validator.addMethod("pattern", function(value, element, param) {
        return this.optional(element) || param.test(value);
    }, "Password must include uppercase, lowercase, number, and special character.");
});

document.querySelectorAll('input').forEach(input => {
    input.setAttribute('autocomplete', 'off');
});
