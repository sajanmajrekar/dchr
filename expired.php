<?php include 'inc/config.php'; ?>
<?php include 'inc/template_start.php'; ?>
<?php  require_once('includes/dbcon.php');  

$sql = "SELECT * from subscription";

$result = $connect->query($sql);

if($result->num_rows > 0) { 

 // $row = $result->fetch_array();
 $active = ""; 
 $i= 1;

 while($row = $result->fetch_array()) {
 	if($row['status'] != 0) {
 	    header("Location:login.php");
 	}
 }
}
?>
<!-- Full Background -->
<!-- For best results use an image with a resolution of 1280x1280 pixels (prefer a blurred image for smaller file size) -->
<img src="img/placeholders/layout/error_full_bg.jpg" alt="Full Background" class="full-bg full-bg-bottom animation-pulseSlow">
<!-- END Full Background -->

<!-- Error Container -->
<div id="error-container">
    <div class="row text-center">
        <div class="col-md-6 col-md-offset-3">
            <h1 class="text-light animation-fadeInQuick"><strong>Subscription Expired!</strong></h1>
            <h2 class="text-light animation-fadeInQuickInv"><em>Contact your service provider.</em></h2>
        </div>
        <div class="col-md-4 col-md-offset-4" style="display:none">
            <form action="page_ready_search_results.php" method="post" class="push">
                <div class="input-group input-group-lg">
                    <input type="text" id="search-term" name="search-term" class="form-control" placeholder="Search <?php echo $template['name']; ?>..">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-effect-ripple btn-primary"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
            <a href="page_ready_blank.php" class="btn btn-effect-ripple btn-default"><i class="fa fa-arrow-left"></i> Go back</a>
        </div>
    </div>
</div>
<!-- END Error Container -->

<?php include 'inc/template_end.php'; ?>