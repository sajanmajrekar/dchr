var statusdatatable;

$(document).ready(function() {
           statusdatatable= $('#statusdatatable').DataTable({
                'ajax': 'php_actions/fetchStatus.php',
                'order': [],
                pageLength: 10,
                lengthMenu: [[5, 10, 20], [5, 10, 20]]
            });
	
});
function editStatus(StatusId = null) {
    $('#EditstatusForm').get(0).reset();
    if(StatusId) {
        $.ajax({
            url: 'php_actions/fetchSelectedStatus.php',
            type: 'post',
            data: {StatusId: StatusId},
            dataType: 'json',
            success:function(response) {  
                $("#EditStatus-name").val(response.name);
                $("#Editstatus-id").val(response.id);
            }
        });
    }
}

function removeStatus(statusid = null) {
    var r = confirm("Do you really want to remove?");
    if(r){
	if(statusid) {
		$.ajax({
				url: 'php_actions/removeStatus.php',
				type: 'post',
				data: {statusid: statusid},
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
                                                 statusdatatable.ajax.reload(null, false);
				} // /success function
			}); // /ajax fucntion to remove the product
			return false;
		}
    }else{
        return false;
    }
}
$('#addstatusform').validate({
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
                    'addstatus-name': {
                        required: true
                    }
                },
                messages: {
                    'addstatus-name': {
                        required: 'Please enter a status name'
                }
            },
                 submitHandler: function(form) {
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/createStatus.php',
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
                                                    $('#addStatusModal').modal('hide');
                                                    $('#addstatusform').get(0).reset();
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                }
                                                statusdatatable.ajax.reload(null, false);
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                 }
            });
            $('#EditstatusForm').validate({
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
                    'EditStatus-name': {
                        required: true
                    }
                },
                messages: {
                    'EditStatus-name': {
                        required: 'Please enter a Status name'
                    }
                },
                 submitHandler: function(form) {
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/UpdateSelectedStatus.php',
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
                                                    $('#editStatusModal').modal('hide');
                                                    $('#EditstatusForm').get(0).reset();
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                }
                                                statusdatatable.ajax.reload(null, false);
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                 }
            });