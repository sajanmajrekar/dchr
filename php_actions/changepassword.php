<?php 
include("header.php");
if (isset($_POST['submit']))
 {
		$user_id= $_SESSION['id'];
		
	
	$oldpass=md5($_POST['oldpass']);
	$newpass=md5($_POST['newpass']);
	$query=query("select * from admin_user where id=$user_id and password='$oldpass'");
	  if($query->num_rows != 0){
		$data = array(
		'password' => $newpass,
		);
		$where = array(
		'id' => $user_id,
);
		update('admin_user',$data,$where);
		   redirect('logout.php?message= Password Updated');
	   }
	   else{
	   	echo "<script>alert('The Password you have Inserted is incorrect')</script>";
	   }
  }

?>
<style> .asa{color: #FFF !important;background-color: #7ac14e !important;border-color: #7ac14e !important;}
.asa:hover{color: #3a7f3c !important;background-color:transparent !important;border-color: #3a7f3c !important;}
</style>
            <!-- END SIDEBAR -->
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <!-- BEGIN CONTENT BODY -->
                <div class="page-content">
                    <!-- BEGIN PAGE HEADER-->
                    <!-- BEGIN THEME PANEL -->
                    
                    <!-- END THEME PANEL -->
                    <!-- BEGIN PAGE BAR -->
                    <div class="page-bar">
                      
                    </div>
                    <!-- END PAGE BAR -->
                    <!-- BEGIN PAGE TITLE-->
        

       <!--  List Start -->

                            <!-- END BEGIN PROFILE SIDEBAR -->
                            <!-- BEGIN PROFILE CONTENT -->
                            <div class="profile-content">
                                <div class="row">
                                <div class="col-md-3">
                                </div>
                                    <div class="col-md-7">
                                        <div class="portlet light ">
<!--                                             <div class="portlet-title tabbable-line">
                                                <div class="caption caption-md">
                                                    <i class="icon-globe theme-font hide"></i>
                                                    <span class="caption-subject font-blue-madison bold uppercase">Profile Account</span>
                                                </div>
                                                <ul class="nav nav-tabs">
                                                    <li>
                                                        <a href="#tab_1_3" data-toggle="tab">Change Password</a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab_1_4" data-toggle="tab">Privacy Settings</a>
                                                    </li>
                                                </ul>
                                            </div> -->
                                            <div class="portlet-body">
                                                <div class="tab-content">
                                                    <!-- PERSONAL INFO TAB -->
                                                  
                                                    <!-- END PERSONAL INFO TAB -->
                                                    <!-- CHANGE AVATAR TAB -->
                                                   
                                                    <!-- END CHANGE AVATAR TAB -->
                                                    <!-- CHANGE PASSWORD TAB -->
<!--                                                     <div class="tab-pane" id="tab_1_3">
 -->                                                        <form action="changepassword.php" method="post" style="padding: 0 15px;">
                                                            <h3 style="font-weight: 500;text-align: left;padding-bottom: 10px;">Reset Password</h3>
															<div class="form-group">
                                                                <label class="control-label">Current Password</label>
                                                                <input type="password"  name="oldpass" class="form-control" required /> </div>
                                                            <div class="form-group">
                                                                <label class="control-label">New Password</label>
                                                                <input type="password" name="newpass" id="password" class="form-control" required/> </div>
                                                            <div class="form-group">
                                                                <label class="control-label">Re-type New Password</label>
                                                                <input type="password" id="confirmpassword" onfocusout="validatePassword()"  class="form-control" required/> </div>
                                                            <div class="margin-top-10 margin-bottom-25">
                                                                <button class="btn green asa" name="submit" type="submit"> Change Password and Logout</button>
                                                            </div>
                                                        </form>
<!--                                                     </div>
 -->                                                    <!-- END CHANGE PASSWORD TAB -->
                                                    <!-- PRIVACY SETTINGS TAB -->
                                               
                                                    <!-- END PRIVACY SETTINGS TAB -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                                                    <div class="col-md-2">
                                </div>
                                </div>
                            </div>
                            <!-- END PROFILE CONTENT -->
                        </div>
                    </div>
                </div>
                <!-- END CONTENT BODY -->
            </div>
            <!-- END CONTENT -->
            <!-- BEGIN QUICK SIDEBAR -->
             <script type="text/javascript" src="http://www.technicalkeeda.com/js/javascripts/plugin/jquery.js"></script>
    <script type="text/javascript" src="http://www.technicalkeeda.com/js/javascripts/plugin/jquery.validate.js"></script>
                <script>
    function validatePassword() {
      var password=$("#password").val();
      var confirmpassword=$("#confirmpassword").val();
      if (password!=confirmpassword) {
      	alert('password Deos not match');
      	$('button').prop('disabled', true);
      }
   if (password==confirmpassword) {
      	$('button').prop('disabled', false);
      }

      
    }
 
    </script>
          <?php include('footer.php');?>