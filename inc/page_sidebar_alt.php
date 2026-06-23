<?php
/**
 * page_sidebar_alt.php
 *
 * Author: pixelcave
 *
 * The alternative sidebar of each page
 *
 */



?>
<!-- Alternative Sidebar -->
<div id="sidebar-alt" tabindex="-1" aria-hidden="true">
    <!-- Toggle Alternative Sidebar Button (visible only in static layout) -->
    <a href="javascript:void(0)" id="sidebar-alt-close" onclick="App.sidebar('toggle-sidebar-alt');"><i class="fa fa-times"></i></a>

    <!-- Wrapper for scrolling functionality -->
    <div id="sidebar-scroll-alt">
        <!-- Sidebar Content -->
        <div class="sidebar-content">
            <!-- Profile -->
            <div class="sidebar-section">
                <h2 class="text-light">Profile</h2>
                <form action="" id="page-sidebar-alt" method="post" class="form-control-borderless">

                    <?php
                    $query = query("Select * from tblstaff where email='".$email."'");
                    $row = $query->fetch_object(); 
                    ?>
                     <input type="hidden" class="form-control" name="id" value="<?php echo $row->staffid;?>">
                    <div class="form-group">
                        <div>
                            <label for="side-profile-name">First Name</label>
                            <input type="text" id="side-profile-fname" name="side-profile-fname" class="form-control" value="<?php echo $row->firstname;?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div>
                            <label for="side-profile-name">Last Name</label>
                            <input type="text" id="side-profile-lname" name="side-profile-lname" class="form-control" value="<?php echo $row->lastname;?>">
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <div>
                            <label for="side-profile-email">Email</label>
                            <input type="email" id="side-profile-email" name="side-profile-email" class="form-control" value="<?php echo $row->email;?>">
                        </div>
                    </div>
                     <div class="form-group">
                        <div>
                            <label for="side-profile-phone">Phone</label>
                            <input type="text" id="side-profile-phone" name="side-profile-phone" class="form-control" value="<?php echo $row->phonenumber;?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div>
                            <label for="side-profile-password">New Password</label>
                            <input type="password" id="side-profile-password" name="side-profile-password" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div>
                            <label for="side-profile-password-confirm">Confirm New Password</label>
                            <input type="password" id="side-profile-password-confirm" name="side-profile-password-confirm" class="form-control">
                        </div>
                    </div>
                    <div class="form-group remove-margin form-actions">
                        <button type="submit" class="btn btn-effect-ripple btn-primary">Save</button>
                    </div>
                </form>
            </div>
            <!-- END Profile -->

            <!-- Settings 
            <div class="sidebar-section">
                <h2 class="text-light">Settings</h2>
                <form action="index.php" method="post" class="form-horizontal form-control-borderless" onsubmit="return false;">
                    <div class="form-group">
                        <label class="col-xs-7 control-label-fixed">Notifications</label>
                        <div class="col-xs-5">
                            <label class="switch switch-success"><input type="checkbox" checked><span></span></label>
                        </div>
                    </div>
                    <div class="form-group remove-margin">
                        <button type="submit" class="btn btn-effect-ripple btn-primary" onclick="App.sidebar('close-sidebar-alt');">Save</button>
                    </div>
                </form>
            </div>
                 END Settings -->
        </div>
        <!-- END Sidebar Content -->
    </div>
    <!-- END Wrapper for scrolling functionality -->
</div>
<!-- END Alternative Sidebar -->

