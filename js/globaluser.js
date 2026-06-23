$(document).ready(function() {
    App.datatables();
});
var FormsValidation = function() {

    return {
        init: function() {
            /*
             *  Jquery Validation, Check out more examples and documentation at https://github.com/jzaefferer/jquery-validation
             */

            /* Initialize Form Validation */
            $('#page-sidebar-alt').validate({
                errorClass: 'help-block animation-pullUp', // You can change the animation class for a different entrance animation - check animations page
                errorElement: 'div',
                errorPlacement: function(error, e) {
                    e.parents('.form-group > div').append(error);
                },
                highlight: function(e) {
                    $(e).closest('.form-group').removeClass('has-success has-error').addClass('has-error');
                    $(e).closest('.help-block').remove();
                },
                success: function(e) {
                    // You can use the following if you would like to highlight with green color the input after successful validation!
                    e.closest('.form-group').removeClass('has-success has-error'); // e.closest('.form-group').removeClass('has-success has-error').addClass('has-success');
                    e.closest('.help-block').remove();
                    
                },
                rules: {
                    'side-profile-fname': {
                        required: true
                    },
                    'side-profile-lname': {
                        required: true
                    },
                    'side-profile-phone':{
                    	required: true,
                    	number: true
                    },
                    'side-profile-email': {
                        required: true,
                        email: true
                    },
                    'side-profile-password': {
                    	required: true,
                        minlength: 8
                    },
                    'side-profile-password-confirm': {
                        equalTo: '#side-profile-password'
                    },
                },
                messages: {
                    'side-profile-fname': {
                        required: 'Please enter a first name',
                        minlength: 'Your username must consist of at least 3 characters'
                    },
                    'side-profile-lname': {
                        required: 'Please enter a last name',
                        minlength: 'Your username must consist of at least 3 characters'
                    },
                    'side-profile-email': 'Please enter a valid email address',
                    'side-profile-phone':  {
                    	required : 'Please enter a phone number',
                        number: 'Please enter a number!'
                    },
                    'side-profile-password': {
                    	required : 'Please enter a password',
                        minlength: 'Your password must be at least 8 characters long'
                    },
                    'side-profile-password-confirm': {
                        minlength: 'Your password must be at least 8 characters long',
                        equalTo: 'Please enter the same password as above'
                    }
                },
                 submitHandler: function(form) {
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/updateUser.php',
                                        type: formactions.attr('method'),
                                        data: formData,
                                        cache: false,
                                        processData: false,
                                        success:function(response) {
                                                console.log(response);
                                                var growlType = 'success';
                                                $.bootstrapGrowl('<p>User details updated successfully!</p>', {
                                                    type: growlType,
                                                    delay: 3000,
                                                    allow_dismiss: true,
                                                    offset: {from: 'top', amount: 20}
                                                });
                                        } // /if response.success
                                        // /success function
                                    });
                 }
            });
        }
    };
}();

