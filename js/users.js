var manageUserTable;

$(document).ready(function() {
	// top nav bar 
	$('#usersection').addClass('active');
	$('.dataTables_filter').addClass('pull-right');
	// manage product data table
	/* Initialize Bootstrap Datatables Integration */
            

            /* Initialize Datatables */
           manageUserTable= $('#userdatatable').DataTable({
                'ajax': 'php_actions/fetchUsers.php',
                'order': [],
                columnDefs: [ { orderable: false, targets: [ 4 ] } ],
                pageLength: 10,
                lengthMenu: [[5, 10, 20], [5, 10, 20]]
            });
	
});
	$(".adduser").click(function(){
		$("#userstable").addClass('hide');
		$("#addusers").removeClass('hide');

	});
	$("#usercancel").click(function(){
		$("#addusers").addClass('hide');
		$("#userstable").removeClass('hide');
	});
function editUser(UserId = null) {
    $('#EditUserForm').get(0).reset();
    if(UserId) {
        $.ajax({
            url: 'php_actions/fetchSelectedUser.php',
            type: 'post',
            data: {UserId: UserId},
            dataType: 'json',
            success:function(response) {  
                $("#Edituser-fname").val(response.firstname);
                $("#Edituser-lname").val(response.lastname);
                $("#Edituser-email").val(response.email);
                $("#Edituser-phone").val(response.phonenumber);
                $("#editUserStatus").val(response.active);
                $("#Edituser-id").val(response.staffid);
                if(response.admin == "1"){
                    $("#Edituser-rights1").prop('checked', true);
                    $("#Edituser-rights2").prop('checked', false);
                }else if(response.admin == "0"){
                    $("#Edituser-rights1").prop('checked', false);
                    $("#Edituser-rights2").prop('checked', true);
                }
            }
        });
    }
}

function removeUser(userid = null) {
    var r = confirm("Do you really want to remove?");
    if(r){
	if(userid) {
		$.ajax({
				url: 'php_actions/removeUser.php',
				type: 'post',
				data: {userid: userid},
				dataType: 'json',
				success:function(response) {
                                                if(response.success==true){
                                                    var growlType = 'success';
                                                    $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
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
				} // /success function
			}); // /ajax fucntion to remove the product
			return false;
		}
    }else{
        return false;
    }
}
$('#adduserform').validate({
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
                    'adduser-fname': {
                        required: true
                    },
                    'adduser-lname': {
                        required: true
                    },
                    'adduser-phone':{
                        required: true,
                        number: true
                    },
                    'adduser-email': {
                        required: true,
                        email: true
                    },
                    'adduser-password': {
                        required: true,
                        minlength: 8
                    },
                    'adduser-passwordconfirm': {
                        equalTo: '#adduser-password'
                    },
                    'action':{
                        required: true,
                    },
                    'rights':{
                        required: true
                    }
                },
                messages: {
                    'adduser-fname': {
                        required: 'Please enter a first name',
                        minlength: 'Your username must consist of at least 3 characters'
                    },
                    'adduser-lname': {
                        required: 'Please enter a last name',
                        minlength: 'Your username must consist of at least 3 characters'
                    },
                    'adduser-email': 'Please enter a valid email address',
                    'adduser-phone':  {
                        required : 'Please enter a phone number',
                        number: 'Please enter a number!'
                    },
                    'adduser-password': {
                        required : 'Please enter a password',
                        minlength: 'Your password must be at least 8 characters long'
                    },
                    'adduser-passwordconfirm': {
                        minlength: 'Your password must be at least 8 characters long',
                        equalTo: 'Please enter the same password as above'
                    },
                    'rights': {
                        required: 'Select the rights'
                    },
                    'action':{
                        required: 'Select the status',
                    }

                },
                 submitHandler: function(form) {
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/createUser.php',
                                        type: formactions.attr('method'),
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
            $('#EditUserForm').validate({
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
                    'Edituser-fname': {
                        required: true
                    },
                    'Edituser-lname': {
                        required: true
                    },
                    'Edituser-phone':{
                        required: true,
                        number: true
                    },
                    'Edituser-email': {
                        required: true,
                        email: true
                    },
                    'Edituser-password': {
                        minlength: 8
                    },
                    'Edituser-passwordconfirm': {
                        equalTo: '#Edituser-password'
                    },
                    'edit-action':{
                        required: true,
                    },
                    'edit-rights':{
                        required: true
                    }
                },
                messages: {
                    'Edituser-fname': {
                        required: 'Please enter a first name',
                        minlength: 'Your username must consist of at least 3 characters'
                    },
                    'Edituser-lname': {
                        required: 'Please enter a last name',
                        minlength: 'Your username must consist of at least 3 characters'
                    },
                    'Edituser-email': 'Please enter a valid email address',
                    'Edituser-phone':  {
                        required : 'Please enter a phone number',
                        number: 'Please enter a number!'
                    },
                    'Edituser-password': {
                        required : 'Please enter a password',
                        minlength: 'Your password must be at least 8 characters long'
                    },
                    'Edituser-passwordconfirm': {
                        minlength: 'Your password must be at least 8 characters long',
                        equalTo: 'Please enter the same password as above'
                    },
                    'edit-rights': {
                        required: 'Select the rights'
                    },
                    'edit-action':{
                        required: 'Select the status',
                    }

                },
                 submitHandler: function(form) {
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/UpdateSelectedUser.php',
                                        type: formactions.attr('method'),
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