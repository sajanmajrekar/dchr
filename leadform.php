<!DOCTYPE html>
<script src="js/vendor/jquery-2.2.4.min.js"> </script>
<!--[if IE 9]>         <html class="no-js lt-ie10" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
    <head>

        <style> 
            body{

                background-color: white !important ;
            }



        </style>
        <meta charset="utf-8">

        <title>AppUI - Web App Bootstrap Admin Template</title>

        <meta name="description" content="AppUI is a Web App Bootstrap Admin Template created by pixelcave and published on Themeforest">
        <meta name="author" content="pixelcave">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0">

        <!-- Icons -->
        <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
        <link rel="shortcut icon" href="img/favicon.png">
        <link rel="apple-touch-icon" href="img/icon57.png" sizes="57x57">
        <link rel="apple-touch-icon" href="img/icon72.png" sizes="72x72">
        <link rel="apple-touch-icon" href="img/icon76.png" sizes="76x76">
        <link rel="apple-touch-icon" href="img/icon114.png" sizes="114x114">
        <link rel="apple-touch-icon" href="img/icon120.png" sizes="120x120">
        <link rel="apple-touch-icon" href="img/icon144.png" sizes="144x144">
        <link rel="apple-touch-icon" href="img/icon152.png" sizes="152x152">
        <link rel="apple-touch-icon" href="img/icon180.png" sizes="180x180">
        <!-- END Icons -->

        <!-- Stylesheets -->
        <!-- Bootstrap is included in its original form, unaltered -->
        <link rel="stylesheet" href="css/bootstrap.min.css">

        <!-- Related styles of various icon packs and plugins -->
        <link rel="stylesheet" href="css/plugins.css">

        <!-- The main stylesheet of this template. All Bootstrap overwrites are defined in here -->
        <link rel="stylesheet" href="css/main.css">

        <!-- Include a specific file here from css/themes/ folder to alter the default theme of the template -->

        <!-- The themes stylesheet of this template (for using specific theme color in individual elements - must included last) -->
        <link rel="stylesheet" href="css/themes.css">
        <!-- END Stylesheets -->

        <!-- Modernizr (browser feature detection library) -->
        <script src="js/vendor/modernizr-3.3.1.min.js"></script>
    </head>
    <body>
        <!-- Full Background -->
        <!-- For best results use an image with a resolution of 1280x1280 pixels (prefer a blurred image for smaller file size) -->
<form id="CreateExpiredForm" action="" class="form-control-borderless">
  <div class="form-group">
    <div>
        <label for="fullname">Name:</label>
        <input type="text" class="form-control" id="fullname" name="fullname" />
    </div>
  </div>
  <div class="form-group">
     <div>
    <label for="phone">Phone Number:</label>
    <input type="password" class="form-control" id="phone" name="phone" />
    </div>
  </div>

  <div class="form-group">
     <div>
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" />
    </div>
  </div>



  <button type="submit" class="btn btn-default center-block">Submit</button>
</form>



        
        <!-- END Full Background -->

        <!-- Error Container -->
        <script src="js/vendor/jquery-2.2.4.min.js"></script>
<script src="js/vendor/bootstrap.min.js"></script>
<script src="js/plugins.js"></script>
        <script type="text/javascript">
            

$('#CreateExpiredForm').validate({
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
                    'fullname': {
                        required: true
                    },
                    'phone':{
                        required: true,
                        number: true
                    },
                    'email': {
                        required: true,
                        email: true
                    }
                },
                messages: {
                    'fullname': {
                        required: 'Please enter a name',
                        minlength: 'Your username must consist of at least 3 characters'
                    },
                    'email': 'Please enter a valid email address',
                    'phone':  {
                        required : 'Please enter a phone number',
                        number: 'Please enter a number!'
                    }

                },
                 submitHandler: function(form) {
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/CreateExpiredUser.php',
                                        type: 'POST',
                                        data: formData,
                                        cache: false,
                                        processData: false,
                                        success:function(response) {
                                                var response = JSON.parse(response);
                                                if(response.success==true){
                                                    var growlType = 'success';
                                                    $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                    $('#addUserModal').modal('hide');
                                                    $('#adduserform').get(0).reset();
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                }
                                                manageUserTable.ajax.reload(null, false);
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                 }
            });





        </script>
        
        <!-- END Error Container -->
    </body>
</html>