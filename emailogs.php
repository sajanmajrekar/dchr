<?php include 'inc/config.php'; $template['header_link'] = 'UI ELEMENTS'; ?>
<?php include 'inc/template_start.php'; ?>
<?php include 'inc/page_head.php'; ?>
<style type="text/css">
    
    #leaddatatable_wrapper{
        background: #f9f9f9;
    }
 
#reset{
    margin-top: 23px;
}
#leaddatatable{
    z-index: 999;
    background: #f9f9f9;
}
.ttop{
    position: relative;
}
.focus .open>.dropdown-menu{
    z-index: 99999
}
#candidatetable{
    width:100% !important;
}
.focus .actions{
    visibility: visible;
}
.actions{
    padding: 10px 0;
    background: #f9f9f9;
    top: 0; 
    position: absolute;
    width: 100%;
    visibility: hidden;
    z-index: 10;
    animation-duration: 0.5s;
    animation-fill-mode: both;
}
.dataTables_length, .dt-buttons, #leaddatatable_filter{
    /*transform: translate(0,-50px);*/
}
.dropdown-menu i{
    opacity: 1
}
.pdfion{
    width: 15px;
    margin-right: 10px;
    display: inline-block;
    margin-bottom: 10px;
}
.pdfion img{
    width: 15px;
}
.pdfion a{
    display: inline-block;  
}
.dummy-table thead tr th{
    vertical-align: middle;
}
.download-btn{
    margin-bottom: 20px;
}
#mailform .row{
    margin-bottom: 10px;
}
.fadeInBottom { animation-name: fadeInBottom }
@keyframes fadeInBottom {
    from {
        opacity: 0
    }
    to { opacity: 1;}
}
</style>
<!-- Page content -->
<div id="page-content">
    <!-- Table Styles Header -->
    <div class="content-header">
        <div class="row">
            <div class="col-sm-6">
                <div class="header-section">
                    <h1>Email Logs</h1>
                </div>
            </div>
            <div class="col-sm-6 hidden-xs">
                <div class="header-section">
                    <ul class="breadcrumb breadcrumb-top">
                        <li>Dashboard</li>
                        <li><a href="#">Email Logs</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- END Table Styles Header -->

    <!-- Datatables Block -->
    <!-- Datatables is initialized in js/pages/uiTables.js -->
    <div class="block full">
        <div class="block-title">
            <h2>Email Logs</h2>
            
        </div>

        <div class="table-responsive">
           <table id="leaddatatable" class="table table-striped table-bordered table-vcenter">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 75px;"><i class="fa fa-flash"></i></th>
                        <th>Subject</th>
                        <th>Mail Sent on</th>
                        <th>Sent By</th>
                        
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- END Datatables Block -->
</div>
<!-- END Page Content -->
<!-- Large Modal -->
               <div  id="editMyLeadModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                <form action="" id="editleadform" method="post" class="form-control-borderless">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header-tabs">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <ul class="nav nav-tabs" data-toggle="tabs">
                                                        <li class="active"><a href="#modal-tabs-home"><i class="fa fa-home"></i> Details</a></li>
                                                        
                                                    </ul>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="tab-content">
                                                        <div class="tab-pane active" id="modal-tabs-home">
                                                         <h4>Email Details</h4>
                                                            <div class="row">
                                                                <input type="hidden" id="Editlead-id" name="Editlead-id" />
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <div>
                                                                            <label for="editlead-name">Subject</label><br>
                                                                            <span class="subject">Subject</span>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    
                                                                </div>
                                                                <div class="col-md-12">
                                                                   <div class="form-group">
                                                                        <div>
                                                                            <label for="editlead-name">Mail Content</label><br>
                                                                            <span class="Content">Content</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <h4 class="sub-header">Candidates</h4>
                                                                    <div class="row">
                                    <div class="col-md-12">
                                            <div class="table-responsive">
                                               <table id="candidatetable" class="table table-striped table-bordered table-vcenter">
                                                    <thead>
                                                        <tr>
                                                            <th>Sr no.</th>
                                                            <th>Name</th>
                                                            <th>Roles</th>
                                                            <th>Date added</th>
                                                            <th>Last contacted</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                        
                                    </div>       
                            </div>
                                                                </div>
                         
                                                        </div>
                                                       
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                <!-- END Large Modal -->
                <!-- Large Modal -->
                <div  class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <form action="" id="EditUserForm" method="post" class="form-control-borderless">
                            <input type="hidden" name="Edituser-id" id="Edituser-id" class="form-control" value="">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="modal-title"><strong>Edit User</strong></h3>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div>
                                                <label for="adduser-fname">First Name</label>
                                                <input type="text" id="Edituser-fname" name="Edituser-fname" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <label for="Edituser-lname">Last Name</label>
                                                <input type="text" id="Edituser-lname" name="Edituser-lname" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <label for="Edituser-email">Email</label>
                                                <input type="email" id="Edituser-email" name="Edituser-email" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                                        <label class="">Status: </label>
                                                        <div>
                                                            <select class="form-md-line-select md-form form-control" id="editUserStatus" name="edit-action">
                                                                  <option value="" disabled selected>Choose your action</option>
                                                                  <option value="1">Active</option>
                                                                  <option value="0">Deactive</option>
                                                            </select>
                                                        </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                             <div class="form-group">
                                                <div>
                                                    <label for="phone">Phone</label>
                                                    <input type="text" id="Edituser-phone" name="Edituser-phone" class="form-control" value="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <label for="Edituser-password">New Password</label>
                                                    <input type="password" id="Edituser-password" name="Edituser-password" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <label for="adduser-passwordconfirm">Confirm New Password</label>
                                                    <input type="password" id="Edituser-passwordconfirm" name="Edituser-passwordconfirm" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                            <label class="">User Access: </label>
                                                            <div>
                                                            <label class="radio-inline" for="rights1">
                                                                <input type="radio" id="Edituser-rights1" name="edit-rights" value="1"> Admin
                                                            </label>
                                                            <label class="radio-inline" for="rights2">
                                                                <input type="radio" id="Edituser-rights2" name="edit-rights" value="0"> User
                                                            </label>
                                                        </div>
                                        </div>
                                    </div>

                                </div>
                                    

                            </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit"  class="btn btn-effect-ripple btn-primary">Save</button>
                                <button type="button" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                        </div>
                </div>
                <!-- END Large Modal -->
                 <!-- Large Modal -->
                <div id="emailmodal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <form action="" id="mailform" method="post" class="form-control-borderless">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="modal-title"><strong>Send Email</strong></h3>
                            </div>
                            <div class="modal-body">
                            <div class="row">
                                <div class="col-md-3">
                                     <label for="lead-name">From</label>
                                </div>
                                <div class="col-md-9">
                                     <label for="lead-name">Digichefs HR<span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                     <label for="lead-name">Choose Email Template</label>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <div>
                                            <select id="example-chosen" name="example-chosen" class="select-chosen" data-placeholder="Choose Email Template*" style="width: 250px;">
                                                <option></option>
                                                <option value="1">JD for Social Media Team Lead</option>
                                                <option value="2">JD for SEO</option>
                                                 <option value="3">JD for BD Intern</option>
                                                 <option value="4">JD for Client Servicing/Project Manager</option>
                                                 <option value="5">JD for Web Development Intern</option>
                                                 <option value="6">JD for Front End development</option>
                                                  <option value="17">JD for HR Intern</option>
                                                 <option value="7">JD for HR Executive</option>
                                                 <option value="8">JD for Paid Manager</option>
                                                 <option value="9">JD for SEM Executive</option>
                                                 <option value="10">JD for SEO Intern</option>
                                                 <option value="11">JD for SEO Executive</option>
                                                 <option value="12">JD for Intern Copywriter</option>
                                                 <option value="13">JD for Senior Copywriter</option>
                                                 <option value="14">JD for Graphic Designer</option>
                                                 <option value="15">JD for Social Media Analyst</option>
                                                 <option value="16">JD for Social Media Team Lead</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                     <label for="lead-name">To<span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <div>
                                            <select id="email-chosen-multiple" name="email-chosen-multiple[]" class="newselect" data-placeholder="Roles" style="width: 250px;" multiple>
                                                    <option value selected>Please select</option> 
                                                <?php

                                                    $sql = "SELECT * FROM `tblleads` order by name asc";
                                                    $result = $connect->query($sql);
                                                    if($result->num_rows > 0) { 
                                                     while($row = $result->fetch_array()) {
                                                ?>
                                                    <option value=<?php echo $row['id']; ?>><?php echo $row['email']; ?></option>
                                                <?php }
                                                }?>
                                            </select>
                                         </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                     <label for="lead-name">Subject<span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                            <div>
                                                <input type="text" id="subject" class="form-control" name="subject">
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div>
                                            <label for="lead-name">Message<span class="text-danger">*</span></label>
                                            <textarea id="textarea-ckeditor" name="textarea-ckeditor" class="ckeditor"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>      

                            </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit"  class="btn btn-effect-ripple btn-primary">Send</button>
                                <button type="button" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                        </div>
                </div>
<?php include 'inc/page_footer.php'; ?>
<?php include 'inc/template_scripts.php'; ?>

<!-- Load and execute javascript code used only in this page -->
<script src="https://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>
<script src="js/pages/uiTables.js"></script>
<script src="js/emaillog.js"></script>
<script src="js/datatable/dataTables.buttons.min.js"></script>
<script src="js/datatable/buttons.flash.min.js"></script>
<script src="js/datatable/jszip.min.js"></script>
<script src="js/datatable/pdfmake.min.js"></script>
<script src="js/datatable/vfs_fonts.js"></script>

<script src="js/datatable/buttons.print.min.js"></script>
<script src="js/datatable/buttons.html5.min.js"></script>
<script>$(function(){ UiTables.init(); });
</script>
<!-- ckeditor.js, load it only in the page you would like to use CKEditor (it's a heavy plugin to include it with the others!) -->
        <script src="js/plugins/ckeditor/ckeditor.js"></script>

        <!-- Load and execute javascript code used only in this page -->
        <script src="js/pages/formsComponents.js"></script>


<?php include 'inc/template_end.php'; ?>