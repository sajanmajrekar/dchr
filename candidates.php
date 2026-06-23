<?php include 'inc/config.php'; $template['header_link'] = 'UI ELEMENTS'; ?>
<?php include 'inc/template_start.php'; ?>
<?php include 'inc/page_head.php'; ?>
<style type="text/css">
    .dt-buttons{
        display: inline-block;
        padding: 0px 15px !important;
        border:0 !important;
    }
    #leaddatatable_wrapper{
        background: #f9f9f9;
    }
    .dt-button{
        margin-right: 10px;
    }
    div#leaddatatable_filter {
        display: inline-block;
        float: right;
        border: 0;
        padding: 0 15px 12px 15px;
    }
.dataTables_wrapper > div{
        padding: 8px 15px 5px 9px;
}
div.dataTables_paginate{
      float: right;
    margin-top: 12px;
    border: 0;
    position: relative;
    z-index: 20;
    display: flex;
    gap: 10px;
    justify-content: space-between;
}
div.dataTables_paginate .pagination{
    margin: 0;
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
div.dataTables_paginate .paginate_button{
    margin: 0 !important;
}
div.dataTables_paginate .pagination > li > a,
div.dataTables_paginate .pagination > li > span{
    border-radius: 4px !important;
    margin: 0 4px;
    min-width: 38px;
    text-align: center;
    padding: 8px 12px;
}
#Leadform .modal-body{
    padding-top: 10px
}
.sub-header{
    font-weight: bold;
    margin-top: 0px;
    margin-bottom: 15px;
}
div.dataTables_info{
    padding-top: 12px;
    min-height: 50px;
}
.datefilters{
    padding-top: 0px;
    padding-bottom: 20px;
    margin-right: 0;
    margin-left:0;
}
.btn-search{
    margin-top: 23px;
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
                    <h1>Candidates</h1>
                </div>
            </div>
            <div class="col-sm-6 hidden-xs">
                <div class="header-section">
                    <ul class="breadcrumb breadcrumb-top">
                        <li>Dashboard</li>
                        <li><a href="#">Candidates</a></li>
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
            <h2>Candidates</h2>
            <div class="btn-group pull-right">
                <button class="btn btn-success" data-target="#addLeadImportModal" data-toggle="modal"><i class="fa fa-download"></i>  Import Candidates</button>
                <button class="btn btn-danger" data-target="#addLeadModal" data-toggle="modal"><i class="fa fa-plus"></i>  Add Candidate</button>
            </div>
        </div>

        <div class="table-responsive">
            <div class="row datefilters">
             <div class="form-group">
                    <div class="col-md-2">
                         <label class="control-label" for="example-daterange1">Roles</label>
                          <select id="roles-filter" name="roles-filter" class="form-control" size="1">
                                                    <option value="">Please select</option>
                                                 <?php

                                                    $sql = "SELECT * FROM `tblrole` order by name asc";
                                                    $result = $connect->query($sql);
                                                    if($result->num_rows > 0) { 
                                                     while($row = $result->fetch_array()) {
                                                ?>
                                                    <option value=<?php echo $row[0]; ?>><?php echo $row[1]; ?></option>
                                                <?php }
                                            }?>
                          </select>
                       <!--  <div class="input-group input-daterange" data-date-format="yyyy-mm-dd">
                            <input type="text" name="start_date" id="start_date" class="form-control" autocomplete="off" placeholder="From">
                            <span class="input-group-addon"><i class="fa fa-chevron-right"></i></span>
                            <input type="text" name="end_date" id="end_date" class="form-control" autocomplete="off" placeholder="To">
                        </div> -->
                        </div>
                         <div class="col-md-2">
                         <label class="control-label" for="example-daterange1">Experience</label>
                          <input  id="experiancefilt" name="experiancefilt" class="form-control" type="text" />
                       <!--  <div class="input-group input-daterange" data-date-format="yyyy-mm-dd">
                            <input type="text" name="start_date" id="start_date" class="form-control" autocomplete="off" placeholder="From">
                            <span class="input-group-addon"><i class="fa fa-chevron-right"></i></span>
                            <input type="text" name="end_date" id="end_date" class="form-control" autocomplete="off" placeholder="To">
                        </div> -->
                        </div>
                        <div class="form-group col-md-2">
                                            <div>
                                                <label for="leadstatus">Status</label>
                                                <select id="leadstatus" name="leadstatus" class="form-control" size="1">
                                                    <option value="">Please select</option>
                                                <?php

                                                    $sql = "SELECT * FROM `tblleadsstatus` order by name asc";
                                                    $result = $connect->query($sql);
                                                    if($result->num_rows > 0) { 
                                                     while($row = $result->fetch_array()) {
                                                ?>
                                                    <option value=<?php echo $row[0]; ?>><?php echo $row[1]; ?></option>
                                                <?php }
                                            }?>
                                                </select>
                                            </div>
                                             
                        </div>
                        <div class="form-group col-md-2">
                                            <div>
                                                <label for="leadsource">Source</label>
                                                <select id="leadsource" name="leadsource" class="form-control" size="1">
                                                    <option value="">Please select</option>
                                                <?php

                                                    $sql = "SELECT * FROM `tblleadssources` order by name asc";
                                                    $result = $connect->query($sql);
                                                    if($result->num_rows > 0) { 
                                                     while($row = $result->fetch_array()) {
                                                ?>
                                                    <option value=<?php echo $row[0]; ?>><?php echo $row[1]; ?></option>
                                                <?php }
                                            }?>
                                                </select>
                                            </div>
                                             
                        </div>
                    <div class="form-group col-md-2">
                        <input type="button" name="search" id="search" value="Search" class="btn btn-info btn-search" />
                        <input type="button" name="reset" id="reset" value="Reset" class="btn  btn-danger" />
                    </div>
             <div class="col-md-2">
                 <div>
                    <label for="leadsource">Sort by:</label>
                    <select id="sortbyinterval" name="leadstatus" class="form-control" size="1">
                        <option value="">Please select</option>
                        <option value="last-seven">Last 7 days</option>
                        <option value="last-thirty">Last 30 days</option>
                        <option value="last-month">Last 3 month</option>
                    </select>
                </div>
             </div>
             </div>
            </div>
<button id="downloadButton">Download CSV</button>

    <script>
        document.getElementById('downloadButton').addEventListener('click', function() {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'download.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.responseType = 'blob'; // Receive response as a Blob (binary data)

            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Create a temporary anchor element
                    var a = document.createElement('a');
                    var url = window.URL.createObjectURL(xhr.response);
                    a.href = url;
                    a.download = 'leads.csv';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                } else {
                    console.error('Failed to download CSV file');
                }
            };

            xhr.send('submit=true'); // Send the POST request with a parameter
        });
    </script>
            <table id="leaddatatable" class="table table-striped table-bordered table-vcenter">
                <thead>
                    <tr>
                         <th class="text-center"><label class="csscheckbox csscheckbox-primary"><input class="checkmark" type="checkbox"><span></span></label></th>
                         <th class="text-center" style="width: 75px;"><i class="fa fa-flash"></i></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone number</th>
                        <th>City</th>
                        <th>Roles</th>
                        <th>Experience</th>
                        <th>Current CTC</th>
                        <th>Expected CTC</th>
                        <th>Notice Period</th>
                        <th>Date Added</th>
                        <th>last contacted</th>
                        <!--<th>Source</th>-->
                        <th>Status</th>
                        
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
                                                        <li><a href="#modal-tabs-settings" data-toggle="tooltip" title="Settings"><i class="fi fi-log"></i> Activity log</a></li>
                                                    </ul>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="tab-content">
                                                        <div class="tab-pane active" id="modal-tabs-home">
                                                         <h4 class="sub-header">Basic Information</h4>
                                                            <div class="row">
                                                                <input type="hidden" id="Editlead-id" name="Editlead-id" />
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <div>
                                                                            <label for="editlead-name">Name<span class="text-danger">*</span></label>
                                                                            <input type="text" id="editlead-name" class="form-control" name="editlead-name">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div>
                                                                            <label for="editlead-email">Email<span class="text-danger">*</span></label>
                                                                            <input type="text" id="editlead-email" class="form-control" name="editlead-email">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div>
                                                                            <label for="editlead-phone">Phone<span class="text-danger">*</span></label>
                                                                            <input type="text" id="editlead-phone" class="form-control" name="editlead-phone">
                                                                        </div>
                                                                    </div>
                                                                     <div class="form-group">
                                                                        <div>
                                                                            <label for="editleadadded">Added Date</label>
                                                                            <input type="text" id="editleadadded" class="form-control" name="editleadadded"  disabled="true">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                   
                                                                    <div class="form-group">
                                                                        <div>
                                                                            <label for="editleadlast">Last Updated</label>
                                                                            <input type="text" id="editleadlast" class="form-control" name="editleadlast" disabled="true">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div>
                                                                            <label for="editleadstatus">Status<span class="text-danger">*</span></label>
                                                                            <select id="editleadstatus" name="editleadstatus" class="form-control" size="1">
                                                                                <option value disabled selected>Please select</option>
                                                                            <?php

                                                                                $sql = "SELECT * FROM `tblleadsstatus` order by name asc";
                                                                                $result = $connect->query($sql);
                                                                                if($result->num_rows > 0) { 
                                                                                 while($row = $result->fetch_array()) {
                                                                            ?>
                                                                                <option value=<?php echo $row[0]; ?>><?php echo $row[1]; ?></option>
                                                                            <?php }
                                                                        }?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div>
                                                                            <label for="editlead-source">Source<span class="text-danger">*</span></label>
                                                                            <select id="editlead-source" name="editlead-source" class="form-control" size="1">
                                                                                <option value disabled selected>Please select</option>
                                                                            <?php

                                                                                $sql = "SELECT * FROM `tblleadssources` order by name asc";
                                                                                $result = $connect->query($sql);
                                                                                if($result->num_rows > 0) { 
                                                                                 while($row = $result->fetch_array()) {
                                                                            ?>
                                                                                <option value=<?php echo $row[0]; ?>><?php echo $row[1]; ?></option>
                                                                            <?php }
                                                                        }?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <div>
                                                                            <label for="editlead-followup">Follow up</label>
                                                                            <input type="text" id="editlead-followup" class="form-control" name="editlead-followup"  disabled="true">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <div>
                                                                            <label for="editlead-commentbox">Comment</label>
                                                                            <textarea type="text" id="editlead-commentbox" class="form-control" name="editlead-commentbox" style="resize: none" maxlength="120"></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <h4 class="sub-header">Address Information</h4>
                                                                    <div class="row">
                                    <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                    <label for="editstreet">Street</label>
                                                    <input type="text" id="editstreet" class="form-control" name="editstreet">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <label for="editcity">City</label>
                                                    <input type="text" id="editcity" class="form-control" name="editcity">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                    <label for="editcountry">Country</label>
                                                    <input type="text" id="editcountry" class="form-control" name="editcountry">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                 <div>
                                                    <label for="editpincode">Pincode</label>
                                                    <input type="text" id="editpincode" class="form-control" name="editpincode">
                                                </div>
                                            </div>

                                        </div>
                                    </div> 
                                    <h4 class="sub-header">Professional Details</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                    <label for="editexperience">Experience in Months</label>
                                                    <input type="text" id="editexperience" class="form-control" name="editexperience">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <label for="editcjob">Current Job Title</label>
                                                    <input type="text" id="editcjob" class="form-control" name="editcjob">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <label for="editexpected">Expected Salary</label>
                                                    <input type="text" id="editexpected" class="form-control" name="editexpected">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                 <div>
                                                    <label for="editskillset">Skill Set</label>
                                                    <input type="text" id="editskillset" class="form-control" name="editskillset">
                                                </div>
                                            </div>
                                             <div class="form-group">
                                             <div>
                                            <label for="example-chosen-multiple">Role(s) wish to apply for</label>
                                            
                                                <select id="editexample-chosen-multiple" name="editexample-chosen-multiple[]" class="mselect" data-placeholder="Roles" style="width: 250px;" multiple>
                                                    <!-- <option value disabled selected>Please select</option> -->
                                                     <?php

                                                    $sql = "SELECT * FROM `tblrole` order by name asc";
                                                    $result = $connect->query($sql);
                                                    if($result->num_rows > 0) { 
                                                     while($row = $result->fetch_array()) {
                                                ?>
                                                    <option value=<?php echo $row[0]; ?>><?php echo $row[1]; ?></option>
                                                <?php }
                                            }?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="resume" class="resume">
                                            
                                        </div>
                                        <div class="form-group">
                                                <div>
                                                    <label for="editexample-file-input">Resumes</label>
                                                    <input type="file" id="editexample-file-input" name="editexample-file-input">
                                                </div>
                                            </div>
                                        
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                    <label for="editqualification">Highest Qualification Held</label>
                                                    <input type="text" id="editqualification" class="form-control" name="editqualification">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                 <div>
                                                    <label for="editcemployer">Current Employer</label>
                                                    <input type="text" id="editcemployer" class="form-control" name="editcemployer">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <label for="editcsalary">Current Salary</label>
                                                    <input type="text" id="editcsalary" class="form-control" name="editcsalary">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                 <div>
                                                    <label for="editinfo">Additional Info</label>
                                                    <input type="text" id="info" class="form-control" name="editinfo">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                 <div>
                                                    <label for="editnotice">Notice period in days</label>
                                                    <input type="text" id="notice" class="form-control" name="editnotice">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                   <label for="editlead-reference">Reference</label>
                                                   <input type="text" id="editlead-reference" class="form-control" name="editlead-reference"  disabled="true">
                                                </div>
                                            </div>
                                        </div>
                                    </div>        
                            </div>
                                                                </div>
                         
                                                        </div>
                                                        <div class="tab-pane" id="modal-tabs-settings">
                                                            <!-- Timeline Content -->
                                                            <div class="timeline block-content-full">
                                                                <ul class="timeline-list">

                                                                    
                                                                    
                                                                </ul>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-effect-ripple btn-primary">Save</button>
                                                    <button type="button" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
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
                <div id="addLeadImportModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                          <form action="" id="importLeadform" method="post" class="form-control-borderless">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="modal-title"><strong>Add Candidates</strong></h3>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="condition-list">
                                          <div class="download-btn">
                                            <button class="btn btn-primary" type="button" id="downloadsample"><i class="fa fa-cloud-download"></i> Download Sample</button>
                                        </div>
                                          <li>1. Your CSV data should be in the format below. The first line of your CSV file should be the column headers as in the table example. Also make sure that your file is <b>UTF-8</b> to avoid unnecessary <b>encoding problems</b>.</li>
                                          <li class="text-danger">2. Duplicate email rows wont be imported</li>
                                        </ul>


                                    <div class="table-responsive">
                                        <table class="table table-hover dummy-table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="bold"><span class="text-danger">*</span> Namee</th>
                                                        <th class="bold"><span class="text-danger">*</span> Email</th>
                                                        <th class="bold"><span class="text-danger">*</span> Phone</th>
                                                        <th class="bold">Street</th>
                                                        <th class="bold">City</th>
                                                        <th class="bold"> Country </th>
                                                        <th class="bold">Pincode </th>
                                                        <th class="bold">Experience </th>
                                                        <th class="bold">Current Job </th>
                                                        <th class="bold">Expected Salary </th>
                                                        <th class="bold">Skill Set </th>
                                                        <th class="bold">Roles </th>
                                                        <th class="bold">Qualification </th>
                                                        <th class="bold">Current Employer </th>
                                                        <th class="bold">Current Salary </th>
                                                        <th class="bold">Additional Info </th>
                                                        <th class="bold">Notice Period </th>
                                                    </tr>
                                                </thead>
                                              <tbody>
                                                <tr>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                </tr>                  
                                            </tbody>
                                        </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div>
                                                <label for="leadfile">Choose the CSV file<span class="text-danger">*</span></label>
                                                <input type="file" id="leadfile" class="form-control" name="leadfile">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <label for="import-leadsource">Source<span class="text-danger">*</span></label>
                                                <select id="import-leadsource" name="import-leadsource" class="form-control" size="1">
                                                    <option value disabled selected>Please select</option>
                                                <?php

                                                    $sql = "SELECT * FROM `tblleadssources` order by name asc";
                                                    $result = $connect->query($sql);
                                                    if($result->num_rows > 0) { 
                                                     while($row = $result->fetch_array()) {
                                                ?>
                                                    <option value=<?php echo $row[0]; ?>><?php echo $row[1]; ?></option>
                                                <?php }
                                            }?>
                                                </select>
                                            </div>
                                        </div>
                                       
                                    </div>
                                </div>
                                    

                            </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit"  class="btn btn-effect-ripple btn-primary">Import</button>
                                <button type="button" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                        </div>
                </div>
                <!-- END Large Modal -->
                <!-- END Large Modal -->
                <div id="addLeadModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <form id="addcandidate" enctype="multipart/form-data" method="post" class="form-control-borderless">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="modal-title"><strong>Add Candidates</strong></h3>
                            </div>
                            <div class="modal-body">
                            <h4 class="sub-header">Basic Information</h4>
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div>
                                                <label for="name">Name<span class="text-danger">*</span></label>
                                                <input type="text" id="name" class="form-control" name="name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <label for="email">Email<span class="text-danger">*</span></label>
                                                <input type="text" id="email" class="form-control" name="email">
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div>
                                                <label for="phone">Phone<span class="text-danger">*</span></label>
                                                <input type="text" id="phone" class="form-control" name="phone">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <label for="source">Source<span class="text-danger">*</span></label>
                                                <select id="source" name="source" class="form-control" size="1">
                                                    <option value disabled selected>Please select</option>
                                                <?php

                                                    $sql = "SELECT * FROM `tblleadssources` order by name asc";
                                                    $result = $connect->query($sql);
                                                    if($result->num_rows > 0) { 
                                                     while($row = $result->fetch_array()) {
                                                ?>
                                                    <option value=<?php echo $row[0]; ?>><?php echo $row[1]; ?></option>
                                                <?php }
                                            }?>
                                                </select>
                                            </div>
                                        </div>
                                      
                                    </div>
                                </div>
                                <h4 class="sub-header">Address Information</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                    <label for="street">Street</label>
                                                    <input type="text" id="street" class="form-control" name="street">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <label for="city">City</label>
                                                    <input type="text" id="city" class="form-control" name="city">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                    <label for="country">Country</label>
                                                    <input type="text" id="country" class="form-control" name="country">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                 <div>
                                                    <label for="pincode">Pincode</label>
                                                    <input type="text" id="pincode" class="form-control" name="pincode">
                                                </div>
                                            </div>

                                        </div>
                                    </div>        
                                <h4 class="sub-header">Professional Details</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                    <label for="experience">Experience in Month</label>
                                                    <input type="text" id="experience" class="form-control" name="experience">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <label for="cjob">Current Job Title</label>
                                                    <input type="text" id="cjob" class="form-control" name="cjob">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <label for="expected">Expected Salary</label>
                                                    <input type="text" id="expected" class="form-control" name="expected">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                 <div>
                                                    <label for="skillset">Skill Set</label>
                                                    <input type="text" id="skillset" class="form-control" name="skillset">
                                                </div>
                                            </div>
                                             <div class="form-group">
                                             <div>
                                            <label for="example-chosen-multiple">Role(s) wish to apply for</label>
                                            
                                                <select id="example-chosen-multiple" name="example-chosen-multiple[]" class="" data-placeholder="Roles" style="width: 250px;" multiple>
                                                    <!-- <option value disabled selected>Please select</option> -->
                                                     <?php

                                                    $sql = "SELECT * FROM `tblrole` order by name asc";
                                                    $result = $connect->query($sql);
                                                    if($result->num_rows > 0) { 
                                                     while($row = $result->fetch_array()) {
                                                ?>
                                                    <option value=<?php echo $row[0]; ?>><?php echo $row[1]; ?></option>
                                                <?php }
                                            }?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                                <div>
                                                    <label for="example-file-input">Resumes</label>
                                                    <input type="file" id="example-file-input" name="example-file-input">
                                                </div>
                                            </div>
                                        
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div>
                                                    <label for="qualification">Highest Qualification Held</label>
                                                    <input type="text" id="qualification" class="form-control" name="qualification">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                 <div>
                                                    <label for="cemployer">Current Employer</label>
                                                    <input type="text" id="cemployer" class="form-control" name="cemployer">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <label for="csalary">Current Salary</label>
                                                    <input type="text" id="csalary" class="form-control" name="csalary">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                 <div>
                                                    <label for="info">Additional Info</label>
                                                    <input type="text" id="info" class="form-control" name="info">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                 <div>
                                                    <label for="notice">Notice period in days</label>
                                                    <input type="text" id="notice" class="form-control" name="notice">
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>        
                            </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit"  class="btn btn-effect-ripple btn-primary">Add lead</button>
                                <button type="button" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                        </div>
                </div>
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
<script src="js/pages/uiTables.js"></script>
<script src="js/myleads.js"></script>
<script src="js/datatable/dataTables.buttons.min.js"></script>
<script src="js/datatable/buttons.flash.min.js"></script>
<script src="js/datatable/jszip.min.js"></script>
<script src="js/datatable/pdfmake.min.js"></script>
<script src="js/datatable/vfs_fonts.js"></script>

<script src="js/datatable/buttons.print.min.js"></script>
<script src="js/datatable/buttons.html5.min.js"></script>

<script>$(function(){ UiTables.init(); });
$(document).ready(function(){
    $("#leaddatatable_filter").after('<div class="btn-group"> <a class="btn btn-danger  dropdown-toggle" data-toggle="dropdown">Action</a>  <a href="javascript:void(0)" data-toggle="dropdown" class="btn btn-danger dropdown-toggle" aria-expanded="false"><span class="caret"></span></a> <ul class="dropdown-menu dropdown-menu-left text-left"> <li> <a href="#" onclick="sendmail()" data-target="#emailmodal" data-toggle="modal"> <i class="fa fa-mail-reply-all pull-right"></i> Send Email </a> </li><li class="disabled"> <a href="javascript:void(0)"> <i class="fa fa-whatsapp pull-right"></i> Send Whats App SMS </a> </li></ul> </div></div>');
     $("#leaddatatable_wrapper .dataTables_length,#leaddatatable_wrapper .dt-buttons,#leaddatatable_wrapper .dataTables_filter").wrapAll("<div class='filters' />");
     $("#leaddatatable_wrapper .btn-group").wrapAll("<div class='actions' />");
     $(".filters, .actions").wrapAll("<div class='ttop' />");
     
      $('thead').on('click', 'tr input.checkmark', function () {
        var checkbox = [];
            $.each($("input[name='leadcheckbox']:checked"), function(){            
                checkbox.push($(this).val());
            });
            if(checkbox.length <1){
                $(".ttop").removeClass('focus');
                $(".actions").removeClass('fadeInBottom');
            }else{
              $(".ttop").addClass('focus');
                $(".actions").addClass('fadeInBottom');
            }
    });
      $('tbody').on('click', 'tr input.checkmark', function () {
        var checkbox = [];
            $.each($("input[name='leadcheckbox']:checked"), function(){            
                checkbox.push($(this).val());
            });
            if(checkbox.length <1){
                $(".ttop").removeClass('focus');
                $(".actions").removeClass('fadeInBottom');
            }else{
                $(".ttop").addClass('focus');
                $(".actions").addClass('fadeInBottom');
            }
    });
});

$(".ttop").click(function(){
    console.log("abc");
    
    
});
Dropzone.autoDiscover = false;
$(".dropzone").dropzone({
    paramName: "file", // The name that will be used to transfer the file
  maxFilesize: 2, // MB
  acceptedFiles:"application/pdf,.doc,.docx",
});
 
</script>
<!-- ckeditor.js, load it only in the page you would like to use CKEditor (it's a heavy plugin to include it with the others!) -->
        <script src="js/plugins/ckeditor/ckeditor.js"></script>

        <!-- Load and execute javascript code used only in this page -->
        <script src="js/pages/formsComponents.js"></script>
<script>
        $(document).ready(function() {
            $('#submitButton').click(function() {
                $.ajax({
                    url: 'process.php', // URL to the PHP file that will process the request
                    type: 'POST',
                    data: { action: 'buttonClicked' },
                    success: function(response) {
                        alert('Response from server: ' + response);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error: ' + textStatus);
                    }
                });
            });
        });
    </script>

<?php include 'inc/template_end.php'; ?>
