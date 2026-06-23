<?php include 'inc/config.php'; $template['header_link'] = 'UI ELEMENTS'; ?>
<?php include 'inc/template_start.php'; ?>
<?php include 'inc/page_head.php'; 
if($_SESSION['accounttype'] == 'user'){
   echo '<script>window.location.href="index.php";</script>';
   exit();
}?>
<!-- Page content -->
<div id="page-content">
    <!-- Table Styles Header -->
    <div class="content-header">
        <div class="row">
            <div class="col-sm-6">
                <div class="header-section">
                    <h1>Source</h1>
                </div>
            </div>
            <div class="col-sm-6 hidden-xs">
                <div class="header-section">
                    <ul class="breadcrumb breadcrumb-top">
                        <li>Dashboard</li>
                        <li><a href="#">Sources</a></li>
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
            <h2>Source</h2>
            <div class="btn-group pull-right">
                <button class="btn btn-success" data-target="#addSourceModal"  data-toggle="modal"><i class="fa fa-plus"></i>  Add Source</button>
            </div>
        </div>
        <div class="table-responsive">
            <table id="sourcedatatable" class="table table-striped table-bordered table-vcenter">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">Sr no.</th>
                        <th>Name</th>
                        <th>Total Leads</th>
                        <th class="text-center" style="width: 75px;"><i class="fa fa-flash"></i></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- END Datatables Block -->
</div>
<!-- END Page Content -->
<!-- Large Modal -->
                <div id="addSourceModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                          <form action="" id="addSourceform" method="post" class="form-control-borderless">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="modal-title"><strong>Add Source</strong></h3>
                            </div>
                            <div class="modal-body">
                                        <div class="form-group">
                                            <div>
                                                <label for="addsource-name">Source Name <span class="text-danger">*</span></label>
                                                <input type="text" id="addsource-name" name="addsource-name" class="form-control" value="">
                                            </div>
                                        </div>
                            </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit"  class="btn btn-effect-ripple btn-primary">Add Source</button>
                                <button type="button" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                        </div>
                </div>
                <!-- END Large Modal -->
                <!-- Large Modal -->
                <div id="editSourceModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                          <form action="" id="EditSourceForm" method="post" class="form-control-borderless">
                            <input type="hidden" name="Editsource-id" id="Editsource-id" class="form-control" value="">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="modal-title"><strong>Edit Source</strong></h3>
                            </div>
                            <div class="modal-body">
                                        <div class="form-group">
                                            <div>
                                                <label for="EditSource-name">Source Name <span class="text-danger">*</span></label>
                                                <input type="text" id="EditSource-name" name="EditSource-name" class="form-control" value="">
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
            </div>
                <!-- END Large Modal -->

<?php include 'inc/page_footer.php'; ?>
<?php include 'inc/template_scripts.php'; ?>

<!-- Load and execute javascript code used only in this page -->
<script src="js/pages/uiTables.js"></script>
<script src="js/source.js"></script>

<?php include 'inc/template_end.php'; ?>