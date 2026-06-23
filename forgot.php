<?php include 'inc/config.php'; ?>
<?php include 'inc/template_start.php'; 
if(isset($_SESSION['user'])){
   echo '<script>window.location.href="index.php";</script>';
   exit();
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
        <i class="fa fa-cube text-light-op"></i> <strong>DigiChefs</strong>
    </h1>
    <!-- END Login Header -->

    <!-- Login Block -->
    <div class="block animation-fadeInQuick">
        <!-- Login Title -->
        <div class="block-title">
            <h2>Reset password</h2>
        </div>
        <!-- END Login Title -->

        <!-- Login Form -->
        <?php 
        if(isset($_GET["token"])) {
             $token=$_GET["token"];
        }
        else{
            $token='';
        }
       
        $legal = false;
        if(!empty($token)) {
        $sql1 = "SELECT * FROM `password_resets` where token='".$token."'";
            $result = $connect->query($sql1);
            if($result->num_rows == 0) { 
                $legal = false;
                echo "<script>window.onload = function() { var growlType = 'danger';$.bootstrapGrowl('<p>Invalid token session. Enter your email address.</p>', {
                                                        type: growlType,
                                                        delay: 3000,
                                                        allow_dismiss: true,
                                                        offset: {from: 'top', amount: 20}
                                                    });}</script>";
            }else{
                $legal = true;
            }
        }
        if(empty($token) || $legal===false){
        ?>
        <form id="form-forgot" action="" method="post" class="form-horizontal">
            <div class="form-group">
                <label for="login-email" class="col-xs-12">Email</label>
                <div class="col-xs-12">
                    <input type="text" id="login-email" name="login-email" class="form-control" placeholder="Your email..">
                </div>
            </div>
            <div class="form-group form-actions">
                <div class="col-xs-8">
                </div>
                <div class="col-xs-4 text-right">
                    <button type="submit" class="btn btn-effect-ripple btn-sm btn-success">Submit</button>
                </div>
            </div>
        </form>
        <?php 
            }
            else
            {
                $_SESSION['token'] = $token;
                ?>
         <form id="form-password" action="" method="post" class="form-horizontal">
            <div class="form-group">
                <label for="login-password" class="col-xs-12">Password</label>
                <div class="col-xs-12">
                    <input type="password" id="login-password" name="login-password" class="form-control" placeholder="Password">
                </div>
            </div>
            <div class="form-group">
                <label for="login-cpassword" class="col-xs-12">Confirm Password</label>
                <div class="col-xs-12">
                    <input type="password" id="login-cpassword" name="login-cpassword" class="form-control" placeholder="Confirm Password">
                </div>
            </div>
            <div class="form-group form-actions">
                <div class="col-xs-8">
                </div>
                <div class="col-xs-4 text-right">
                    <button type="submit" class="btn btn-effect-ripple btn-sm btn-success">Submit</button>
                </div>
            </div>
        </form>
            
                
                
      <?php } ?>
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
<script src="js/pages/readyLogin.js"></script>
<script src="js/forgot.js"></script>
<script>$(function(){ ReadyLogin.init(); });</script>

<?php include 'inc/template_end.php'; ?>