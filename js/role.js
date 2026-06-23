var sourcedatatable;

$(document).ready(function() {
           sourcedatatable= $('#sourcedatatable').DataTable({
                'ajax': 'php_actions/fetchrole.php',
                'order': [],
                pageLength: 10,
                lengthMenu: [[5, 10, 20], [5, 10, 20]]
            });
	
});
function editSource(SourceId = null) {
    $('#EditSourceForm').get(0).reset();
    if(SourceId) {
        $.ajax({
            url: 'php_actions/fetchSelectedrole.php',
            type: 'post',
            data: {SourceId: SourceId},
            dataType: 'json',
            success:function(response) {  
                $("#EditSource-name").val(response.name);
                $("#Editsource-id").val(response.id);
            }
        });
    }
}

function removeSource(sourceid = null) {
    var r = confirm("Do you really want to remove?");
    if(r){
	if(sourceid) {
		$.ajax({
				url: 'php_actions/removerole.php',
				type: 'post',
				data: {sourceid: sourceid},
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
                                                 sourcedatatable.ajax.reload(null, false);
				} // /success function
			}); // /ajax fucntion to remove the product
			return false;
		}
    }else{
        return false;
    }
}
$('#addSourceform').validate({
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
                    'addsource-name': {
                        required: true
                    }
                },
                messages: {
                    'addsource-name': {
                        required: 'Please enter a source name'
                }
            },
                 submitHandler: function(form) {
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/createrole.php',
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
                                                    $('#addSourceModal').modal('hide');
                                                    $('#addSourceform').get(0).reset();
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                }
                                                sourcedatatable.ajax.reload(null, false);
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                 }
            });
            $('#EditSourceForm').validate({
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
                    'EditSource-name': {
                        required: true
                    }
                },
                messages: {
                    'EditSource-name': {
                        required: 'Please enter a Source name'
                    }
                },
                 submitHandler: function(form) {
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/UpdateSelectedRole.php',
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
                                                    $('#editSourceModal').modal('hide');
                                                    $('#EditSourceForm').get(0).reset();
                                                }else{
                                                    var growlType = 'danger';
                                                    $.bootstrapGrowl('<p>'+JSON.stringify(response.messages).replace(/"/g, "")+'</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });
                                                }
                                                sourcedatatable.ajax.reload(null, false);
                                                
                                        } // /if response.success
                                        // /success function
                                    });
                 }
            });