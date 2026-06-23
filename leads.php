<?php include 'inc/config.php'; $template['header_link'] = 'UI ELEMENTS'; ?>
<?php include 'inc/template_start.php'; ?>
<?php include 'inc/page_head.php'; ?>
<style type="text/css">
    .download-btn{
        margin-bottom: 13px;
    }
</style>

<!-- Page content -->
<div id="page-content">
    <!-- Table Styles Header -->
    <div class="content-header">
        <div class="row">
            <div class="col-sm-6">
                <div class="header-section">
                    <h1>Leads</h1>
                </div>
            </div>
            <div class="col-sm-6 hidden-xs">
                <div class="header-section">
                    <ul class="breadcrumb breadcrumb-top">
                        <li>Dashboard</li>
                        <li><a href="">Leads</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- END Table Styles Header -->

    <!-- Table Styles Block -->
    <div class="block">
        <!-- Table Styles Content -->
        <div class="block-title">
            <h2>All leads</h2>
            <div class="btn-group pull-right">
                <button class="btn btn-success" data-target="#addLeadImportModal" data-toggle="modal"><i class="fa fa-download"></i>  Import Leads</button>
                <button class="btn btn-danger" data-target="#addLeadModal" data-toggle="modal"><i class="fa fa-plus"></i>  Add Lead</button>
            </div>
        </div>
        <div class="table-responsive">
            <table id="leadstable" class="table table-striped table-bordered table-vcenter">
                <thead>
                    <tr>
                        <th  class="text-center"><label class="csscheckbox csscheckbox-primary"><input type="checkbox"><span></span></label></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone number</th>
                        <th>Date Added</th>
                        <th>Source</th>
                    </tr>
                </thead>
            </table>
            <div class="addtomyleads">
                <button class="btn btn-success" id="addleads" data-toggle="modal"><i class="fa fa-plus"></i>  Add to My Leads</button>
            </div>
        </div>
        <!-- END Table Styles Content -->
    </div>
    <!-- END Table Styles Block -->
</div>
<!-- END Page Content -->
<!-- Large Modal -->
                <div id="addLeadImportModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                          <form action="" id="importLeadform" method="post" class="form-control-borderless">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="modal-title"><strong>Add Leads</strong></h3>
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

                                       

                                        <table class="table table-hover table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="bold"><span class="text-danger">*</span> Name</th>
                                                        <th class="bold"><span class="text-danger">*</span> Email</th>
                                                        <th class="bold"><span class="text-danger">*</span> Phone</th>
                                                    </tr>
                                                </thead>
                                              <tbody>
                                                <tr>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                    <td>Sample Data</td>
                                                </tr>                  
                                            </tbody>
                                        </table>
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
                                        <?php if($_SESSION['accounttype'] == 'admin')
                                        {?>
                                        <div class="form-group">
                                            <div>
                                                <label for="import-leadassigned">Responsible (Assignee)</label>
                                                 <select id="import-leadassigned" name="import-leadassigned" class="form-control" size="1">
                                                    <option value="">Please select</option>
                                                 <?php

                                                    $sql1 = "SELECT staffid, firstname, lastname FROM `tblstaff` where admin!=2 and active=1 order by firstname asc";
                                                    $result1 = $connect->query($sql1);
                                                    if($result1->num_rows > 0) { 
                                                     while($row = $result1->fetch_array()) {
                                                    ?>
                                                    <option value=<?php echo $row[0]; ?>><?php echo $row[1]. ' ' .$row[2]; ?></option>
                                                    <?php }
                                                }?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php } ?>
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
                <div id="addLeadModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <form action="" id="Leadform" method="post" class="form-control-borderless">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="modal-title"><strong>Add Leads</strong></h3>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div>
                                                <label for="lead-name">Name<span class="text-danger">*</span></label>
                                                <input type="text" id="lead-name" class="form-control" name="lead-name">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <label for="lead-email">Email<span class="text-danger">*</span></label>
                                                <input type="text" id="lead-email" class="form-control" name="lead-email">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <label for="lead-phone">Phone<span class="text-danger">*</span></label>
                                                <input type="text" id="lead-phone" class="form-control" name="lead-phone">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div>
                                                <label for="leadsource">Source<span class="text-danger">*</span></label>
                                                <select id="leadsource" name="leadsource" class="form-control" size="1">
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
                                        <?php if($_SESSION['accounttype'] == 'admin')
                                        {?>
                                        <div class="form-group">
                                            <div>
                                                <label for="leadassigned">Responsible (Assignee)</label>
                                                 <select id="leadassigned" name="leadassigned" class="form-control" size="1">
                                                    <option value="">Please select</option>
                                                 <?php

                                                    $sql1 = "SELECT staffid, firstname, lastname FROM `tblstaff` where admin!=2 and active=1 order by firstname asc";
                                                    $result1 = $connect->query($sql1);
                                                    if($result1->num_rows > 0) { 
                                                     while($row = $result1->fetch_array()) {
                                                    ?>
                                                    <option value=<?php echo $row[0]; ?>><?php echo $row[1]. ' ' .$row[2]; ?></option>
                                                    <?php }
                                                }?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php }?>
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

<?php include 'inc/page_footer.php'; ?>
<?php include 'inc/template_scripts.php'; ?>

<!-- Load and execute javascript code used only in this page -->
<script src="js/pages/uiTables.js"></script>
<script src="js/lead.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/additional-methods.min.js"></script>
<script>$(function(){ UiTables.init(); });</script>

<?php include 'inc/template_end.php'; ?>