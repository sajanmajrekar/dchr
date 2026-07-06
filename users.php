<?php include 'inc/config.php'; $template['header_link'] = 'UI ELEMENTS'; ?>
<?php include 'inc/template_start.php'; ?>
<?php include 'inc/page_head.php'; 
if($_SESSION['accounttype'] == 'user'){
   echo '<script>window.location.href="index.php";</script>';
   exit();
}?>
<?php $isSuperadmin = isset($_SESSION['accounttype']) && $_SESSION['accounttype'] == 'superadmin'; ?>
<!-- Page content -->
<div id="page-content">
    <!-- Table Styles Header -->
    <div class="content-header">
        <div class="row">
            <div class="col-sm-6">
                <div class="header-section">
                    <h1>Users</h1>
                </div>
            </div>
            <div class="col-sm-6 hidden-xs">
                <div class="header-section">
                    <ul class="breadcrumb breadcrumb-top">
                        <li>Dashboard</li>
                        <li><a href="#">Users</a></li>
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
            <h2>Users</h2>
            <?php if($isSuperadmin){ ?>
            <div class="btn-group pull-right">
                <button class="btn btn-success" data-target="#addUserModal"  data-toggle="modal"><i class="fa fa-plus"></i>  Add User</button>
            </div>
            <?php } ?>
        </div>
        <div class="table-responsive">
            <table id="userdatatable" class="table table-striped table-bordered table-vcenter">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">Sr no.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone number</th>
                        <th>Access Type</th>
                        <th>Status</th>

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
                <div id="addUserModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <form action="" id="adduserform" method="post" class="form-control-borderless">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="modal-title"><strong>Add User</strong></h3>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div>
                                                <label for="adduser-fname">First Name</label>
                                                <input type="text" id="adduser-fname" name="adduser-fname" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <label for="adduser-lname">Last Name</label>
                                                <input type="text" id="adduser-lname" name="adduser-lname" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <label for="adduser-email">Email</label>
                                                <input type="email" id="adduser-email" name="adduser-email" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                                        <label class="">Status: </label>
                                                        <div>
                                                            <select class="form-md-line-select md-form form-control" name="action">
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
                                                    <input type="text" id="adduser-phone" name="adduser-phone" class="form-control" value="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <label for="adduser-password">New Password</label>
                                                    <input type="password" id="adduser-password" name="adduser-password" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <label for="adduser-passwordconfirm">Confirm New Password</label>
                                                    <input type="password" id="adduser-passwordconfirm" name="adduser-passwordconfirm" class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                            <label class="">User Access: </label>
                                                            <div>
                                                            <label class="radio-inline" for="rights1">
                                                                <input type="radio" id="rights1" name="rights" value="1"> Admin
                                                            </label>
                                                            <label class="radio-inline" for="rights2">
                                                                <input type="radio" id="rights2" name="rights" value="0"> User
                                                            </label>
                                                            <?php if($isSuperadmin){ ?>
                                                            <label class="radio-inline" for="rights3">
                                                                <input type="radio" id="rights3" name="rights" value="2"> Superadmin
                                                            </label>
                                                            <?php } ?>
                                                        </div>
                                        </div>
                                    </div>

                                </div>
                                    

                            </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit"  class="btn btn-effect-ripple btn-primary">Add User</button>
                                <button type="button" class="btn btn-effect-ripple btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                        </div>
                </div>
                <!-- END Large Modal -->
                <!-- Large Modal -->
                <div id="editUserModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                            <?php if($isSuperadmin){ ?>
                                                            <label class="radio-inline" for="rights3">
                                                                <input type="radio" id="Edituser-rights3" name="edit-rights" value="2"> Superadmin
                                                            </label>
                                                            <?php } ?>
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

<?php include 'inc/page_footer.php'; ?>
<?php include 'inc/template_scripts.php'; ?>

<!-- Load and execute javascript code used only in this page -->
<script src="js/pages/uiTables.js"></script>
<script src="js/users.js"></script>

<?php include 'inc/template_end.php'; ?>
