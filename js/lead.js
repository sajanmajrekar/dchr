var manageLeadsTable;

$(document).ready(function() {
           manageLeadsTable= $('#leadstable').DataTable({
                'ajax': 'php_actions/fetchLeads.php',
                'order': [],
                columnDefs: [ { orderable: false, targets: [ 4 ] } ],
                pageLength: 10,
                lengthMenu: [[5, 10, 20], [5, 10, 20]]
            });
        $("#addleads").click(function(){
            var checkbox = [];
            $.each($("input[name='leadcheckbox']:checked"), function(){            
                checkbox.push($(this).val());
            });
            if(checkbox.length <1){
                var growlType = 'danger';
                $.bootstrapGrowl('<p>Select the checkboxes!</p>', {
                    type: growlType,
                    delay: 3000,
                    allow_dismiss: true,
                    offset: {from: 'top', amount: 20}
                });
            }else{
                 //alert("My favourite sports are: " + checkbox.join(", "));
                 console.log(checkbox);
                 $.ajax({
                   type: "POST",
                   data: {checkbox:checkbox},
                   url: "php_actions/assignedLeads.php",
                   success: function(response){
                    var response = JSON.parse(response);
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
                        $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                            type: growlType,
                            delay: 3000,
                            allow_dismiss: true,
                            offset: {from: 'top', amount: 20}
                        });
                    }
                        manageLeadsTable.ajax.reload(null, false);
                   
                   }
                });
            }
        });
});
$('#downloadsample').click(function(e) {
    e.preventDefault();  //stop the browser from following
    window.location.href = 'upload/sample_import_file.csv';
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

$('#importLeadform').validate({
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
                    'leadfile': {
                        required: true,
                        extension: "xls|csv"
                    },
                    'import-leadsource': {
                        required: true
                    },
                    'import-leadassigned':{
                        required: true
                    }
                },
                messages: {
                    'leadfile': {
                        required: 'File is required.',
                        extension: 'Please enter a value with a valid extension.'
                    },
                    'import-leadsource': {
                        required: 'Select the source of the leads.'
                    },
                    'import-leadassigned': {
                        required: 'Select the center to assigned the lead.'
                    }
                },
                 submitHandler: function(form) {
                                    var formactions = $(form); 
                                    $.ajax({
                                        url : 'php_actions/importleads.php',
                                        type: formactions.attr('method'),
                                        data: new FormData(form),
                                        contentType: false,
                                        cache: false,
                                        processData:false,
                                        success:function(response) {
                                                console.log(response);
                                                var response = JSON.parse(response);
                                                if(response.success==true){
                                                    var growlType = 'success';
                                                    $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                    $('#addLeadImportModal').modal('hide');
                                                    $('#importLeadform').get(0).reset();
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                }
                                                manageLeadsTable.ajax.reload(null, false); 
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                 }
            });
$('#Leadform').validate({
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
                    'lead-name': {
                        required: true
                    },
                    'lead-email': {
                        required: true,
                        email: true
                    },
                    'lead-phone':{
                        required: true,
                        number: true
                    },
                    'leadsource':{
                        required: true
                    },
                },
                messages: {
                    'lead-name': {
                        required: 'Name is required.',
                        extension: 'Please enter a value with a valid extension.'
                    },
                    'lead-email': {
                        required: 'Please enter email.',
                        email: 'Please enter a valid email address'
                    },
                    'lead-phone': {
                        required: 'Please enter a phone number.',
                        number: 'Phone number invalid.' 
                    },
                    'leadsource': {
                        required: 'Select the source of the leads.'
                    }
                },
                 submitHandler: function(form) {
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/createLead.php',
                                        type: formactions.attr('method'),
                                        data: formData,
                                        cache: false,
                                        processData: false,
                                        success:function(response) {
                                                console.log(response);
                                                var response = JSON.parse(response);
                                                if(response.success==true){
                                                    var growlType = 'success';
                                                    $.bootstrapGrowl('<p>'+response.messages.replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                    $('#addLeadModal').modal('hide');
                                                    $('#Leadform').get(0).reset();
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                }
                                                manageLeadsTable.ajax.reload(null, false); 
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                 }
            });