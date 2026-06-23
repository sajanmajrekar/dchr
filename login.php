<?php include 'inc/config.php'; ?>
<?php include 'inc/template_start.php'; 
if(isset($_SESSION['user'])){
   echo '<script>window.location.href="index.php";</script>';
   exit();
}
?>

<?php  require_once('includes/dbcon.php');  

$sql = "SELECT * from subscription";

$result = $connect->query($sql);

if($result->num_rows > 0) { 

 // $row = $result->fetch_array();
 $active = ""; 
 $i= 1;

 while($row = $result->fetch_array()) {
 	if($row['status'] != 1) {
 	    header("Location:expired.php");
 	}
 }
}
?>

<!-- Full Background -->
<!-- For best results use an image with a resolution of 1280x1280 pixels (prefer a blurred image for smaller file size) -->
<img src="img/placeholders/layout/login2_full_bg.jpg" alt="Full Background" class="full-bg animation-pulseSlow">
<!-- END Full Background -->

<!-- Login Container -->
<div id="login-container">
    <!-- Login Header -->
    <h1 class="h2 text-light text-center push-top-bottom animation-pullDown">
        <!--<i class="fa fa-cube text-light-op"></i> <strong>DigiChefs</strong>-->
        <img style="width:150px;" class="logo" src="img/Breeze-Logo-02.png" />
    </h1>
    <!-- END Login Header -->

    <!-- Login Block -->
    <div class="block animation-fadeInQuick">
        <!-- Login Title -->
        <div class="block-title">
            <h2>Please Login</h2>
        </div>
        <!-- END Login Title -->

        <!-- Login Form -->
        <form id="form-login" action="/hr/php_actions/checklogin.php" method="post" class="form-horizontal">
            <div class="form-group">
                <label for="login-email" class="col-xs-12">Email</label>
                <div class="col-xs-12">
                    <input type="text" id="login-email" name="login-email" class="form-control" placeholder="Your email..">
                </div>
            </div>
            <div class="form-group">
                <label for="login-password" class="col-xs-12">Password</label>
                <div class="col-xs-12">
                    <input type="password" id="login-password" name="login-password" class="form-control" placeholder="Your password..">
                </div>
            </div>
            <div class="form-group form-actions">
                <a href="forgot.php" class="col-xs-12">Forgot password.</a>
            </div>
            <div class="form-group form-actions">
                <div class="col-xs-8">
                    <label class="csscheckbox csscheckbox-primary hide">
                        <input type="checkbox"  id="login-remember-me" name="login-remember-me"><span></span> Remember Me?
                    </label>
                </div>
                <div class="col-xs-4 text-right">
                    <button type="submit" class="btn btn-effect-ripple btn-sm btn-success">Log In</button>
                </div>
            </div>
        </form>
        <!-- END Login Form -->
    </div>
    <!-- END Login Block -->

    <!-- Footer -->
    <footer class="text-muted text-center animation-pullUp">
        <small><span id="year-copy"></span> &copy; <a href="https://DigiChefs.com" target="_blank"><?php echo $template['name']?></a></small>
    </footer>
    <!-- END Footer -->
</div>
<!-- END Login Container -->

<?php include 'inc/template_scripts.php'; ?>

<!-- Load and execute javascript code used only in this page -->
<!--<script src="js/pages/readyLogin.js"></script>-->
<!--<script>$(function(){ ReadyLogin.init(); });</script>-->

<?php include 'inc/template_end.php'; ?>