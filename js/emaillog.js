var manageLeadsTable;
$(document).ready(function() {
	
	// manage product data table
	/* Initialize Bootstrap Datatables Integration */
      manageLeadsTable= $('#leaddatatable').DataTable({
                'ajax': 'php_actions/fetchEmailLogs.php',
                'order': [],
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
    $("#sortbyinterval").change(function(){ 
    var interval = $(this).val();
         $('#leaddatatable').DataTable().destroy();
         manageUserTable= $('#leaddatatable').DataTable({
                            buttons: ['csv', 'excel', 'pdf' ],
                            'order': [],
                            columnDefs: [ { orderable: false, targets: [ 4 ] } ],
                            pageLength: 10,
                            lengthMenu: [[5, 10, 20, 50], [5, 10, 20, 50]],
                            dom: 'lBfrtip',
                            "ajax" : {
                                url:"php_actions/fetchByLastDays.php",
                                type:"POST",
                                data:{
                                 interval:interval
                                }
                               }
                        });
          $(".dt-button").addClass("btn btn-primary");
    });
function editUser(Id = null) {
    var managecandidatedata;
    if(Id) {
        
        $.ajax({
            url: 'php_actions/fetchEmaillogbyid.php',
            type: 'post',
            data: {Id: Id},
            dataType: 'json',
            success:function(response) {  
               //console.log(response);
                $(".subject").text(response.subject);
                $(".Content").html(response.mailcontent);
            }
        });
        
        if ( $.fn.DataTable.isDataTable('#candidatetable') ) {
          $('#candidatetable').DataTable().destroy();
        }
        managecandidatedata= $('#candidatetable').DataTable({
                                "ajax": {
                                    "url": "php_actions/fetchCandidatesLogs.php",
                                    "type": "POST",
                                    data: {Id: Id},
                                    dataType: 'json',
                                  },
                                'order': [],
                                pageLength: 10,
                                lengthMenu: [[5, 10, 20], [5, 10, 20]]
                        }); 
       
    }
}

$('#editleadform').validate({
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
                    'editlead-name': {
                        required: true
                    },
                    'editlead-phone':{
                        required: true,
                        number: true
                    },
                    'editlead-email': {
                        required: true,
                        email: true
                    },
                    'editleadstatus':{
                        required: true,
                    },
                    'editlead-source':{
                        required: true
                    }
                },
                messages: {
                    'editlead-name': {
                        required: 'Please enter a name'
                    },
                    'editlead-email': 'Please enter a valid email address',
                    'editlead-phone':  {
                        required : 'Please enter a phone number',
                        number: 'Please enter a number!'
                    },
                    'editleadstatus': {
                        required : 'Please enter a password'
                    },
                    'editlead-source': {
                        required: 'Please select the source'
                    }
                },
                 submitHandler: function(form) {
                    if(document.getElementById("editexample-file-input").files.length == 0 ){
                                    $("#editleadform").removeAttr("enctype");
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/updateLeadStatus.php',
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
                                                    $('#editMyLeadModal').modal('hide');
                                                    //$('#editleadform').get(0).reset();
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
                                }else{
                                    $('#editleadform').attr("enctype", "multipart/form-data");
                                    var formactions = $(form); 
                                    var formData = $(form).serialize();
                                    $.ajax({
                                        url : 'php_actions/updateLeadStatus.php',
                                        type: formactions.attr('method'),
                                        data: new FormData(form),
                                        cache: false,
                                        processData: false,
                                        contentType: false,
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
                                                    $('#editMyLeadModal').modal('hide');
                                                    //$('#editleadform').get(0).reset();
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
                 }
            });