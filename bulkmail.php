<?php include 'inc/config.php'; $template['header_link'] = 'UI ELEMENTS'; ?>
<?php include 'inc/template_start.php'; ?>
<?php include 'inc/page_head.php'; ?>
<style type="text/css">
/*    .dt-buttons{*/
/*        display: inline-block;*/
/*        padding: 0px 15px !important;*/
/*        border:0 !important;*/
/*    }*/
/*    #leaddatatable_wrapper{*/
/*        background: #f9f9f9;*/
/*    }*/
/*    .dt-button{*/
/*        margin-right: 10px;*/
/*    }*/
/*    div#leaddatatable_filter {*/
/*        display: inline-block;*/
/*        float: right;*/
/*        border: 0;*/
/*        padding: 0 15px 12px 15px;*/
/*    }*/
/*.dataTables_wrapper > div{*/
/*        padding: 8px 15px 5px 9px;*/
/*}*/
/*div.dataTables_paginate{*/
/*    margin-top: -33px;*/
/*}*/
/*div.dataTables_paginate{*/
/*    float: right;*/
/*    margin-top: -47px;*/
/*    border:0;*/
/*}*/
#Leadform .modal-body{
    padding-top: 10px
}
.sub-header{
    font-weight: bold;
    margin-top: 0px;
    margin-bottom: 15px;
}
div.dataTables_info{
    padding-top: 8px;
    height: 50px;
}
.block-title .btn-group {
    margin-top: 7px;
}
.datefilters{
    padding-top: 0px;
    padding-bottom: 20px;
    margin-right: 0;
    margin-left:0;
}
.datefilters .filter-row{
    display: flex;
    flex-wrap: wrap;
    gap: 16px 0;
    margin-bottom: 14px;
}
.datefilters .filter-row:last-child{
    margin-bottom: 0;
}
.datefilters .filter-actions{
    display: flex;
    align-items: flex-end;
    gap: 10px;
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
.dataTables_filter{
    display:none;
}
#mailform .row{
    margin-bottom: 10px;
}
.block.full{
    position:relative;
}
.txt{
    margin-top: 136px;   
}
.disable-overlay{
    position: absolute;
    width: 100%;
    height: 100%;
    margin: -20px;
    background: rgba(0,0,0,0.7);
    z-index: 999;
    color: #fff;
    font-size: 27px;
    text-align: center;
    margin-top: 20p;
    vertical-align: middle;
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
                    <h1>Bulk Mail</h1>
                </div>
            </div>
            <div class="col-sm-6 hidden-xs">
                <div class="header-section">
                    <ul class="breadcrumb breadcrumb-top">
                        <li>Dashboard</li>
                        <li><a href="#">Bulk Mail</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- END Table Styles Header -->

    <!-- Datatables Block -->
    <!-- Datatables is initialized in js/pages/uiTables.js -->
    <div class="block full">
         <?php

                                                    $sql = "SELECT SUM(totalemailsent) as total,  CURDATE() as date FROM `emaillogs` where Date(senttime)= CURDATE()";
                                                    $result = $connect->query($sql);
                                                    if($result->num_rows > 0) { 
                                                     while($row = $result->fetch_array()) {
                                                      if($row['total']>=250){
                                                ?>
                                                 <div class="disable-overlay">
                                                    <div class="txt">You have exceeded the daily limit of bulk email.</div>
                                                </div>
                                                   
                                                <?php }}
                                            }?>
       
        <div class="block-title">
            <h2>Bulk Mail</h2>
            <div class="btn-group pull-right">
                <?php

                                                    $sql = "SELECT SUM(totalemailsent) as total,  CURDATE() as date FROM `emaillogs` where Date(senttime)= CURDATE()";
                                                    $result = $connect->query($sql);
                                                    if($result->num_rows > 0) { 
                                                     while($row = $result->fetch_array()) {
                                                ?>
                                                   <lable class="label label-danger" style="padding: 6px 13px;">Email Daily Limit: <?php if($row['total']!=null){echo $row['total'];}else{ echo "0";}?>/250</lable> 
                                                <?php }
                                            }?>
                
                <!--<button class="btn btn-success" data-target="#addLeadImportModal" data-toggle="modal"><i class="fa fa-download"></i>  Import Candidates</button>-->
                <!--<button class="btn btn-danger" data-target="#addLeadModal" data-toggle="modal"><i class="fa fa-plus"></i>  Add Candidate</button>-->
            </div>
        </div>

        <div class="">
            <div class="row datefilters">
             <div class="form-group">
                    <div class="filter-row">
                    <div class="col-md-3 col-sm-6">
                         <label class="control-label" for="example-daterange1">Date Range</label>
                        <div class="input-group input-daterange" data-date-format="yyyy-mm-dd">
                            <input type="text" name="start_date" id="start_date" class="form-control" autocomplete="off" placeholder="From">
                            <span class="input-group-addon"><i class="fa fa-chevron-right"></i></span>
                            <input type="text" name="end_date" id="end_date" class="form-control" autocomplete="off" placeholder="To">
                        </div>
                        </div>
                        
                        <div class="form-group col-md-2 col-sm-6">
                                            <div>
                                                <label for="leadsource">Roles</label>
                                                <select id="leadsource" name="leadsource" class="form-control" size="1">
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
                                            </div>
                                             
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                                            <div>
                                                <label for="leadexperieance">Experience in month</label>
                                               <input type="text" id="leadexperieance" class="form-control" name="leadexperieance">
                                            </div>
                                             
                        </div>
                        <div class="form-group col-md-2 col-sm-6">
                                            <div>
                                                <label for="noticeperiod">Notice Period in Days</label>
                                               <input type="text" id="noticeperiod" class="form-control" name="noticeperiod">
                                            </div>
                                             
                        </div>
                    <div class="form-group col-md-2 col-sm-6">
                        <label for="bulkstatus">Status</label>
                        <select id="bulkstatus" name="bulkstatus" class="form-control" size="1">
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
                    <div class="form-group col-md-1 col-sm-6 filter-actions">
                        <input type="button" name="search" id="search" value="Show Datasource" class="btn btn-info btn-search" />
                        <input type="button" name="reset" id="reset" value="Reset" class="btn btn-danger" />
                    </div>
                    </div>
                    <div class="filter-row">
                        <div class="col-md-2 col-sm-6">
                            <label for="bulksource">Source</label>
                            <select id="bulksource" name="bulksource" class="form-control" size="1">
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
                        <div class="col-md-2 col-sm-6">
                            <label for="bulkcity">City</label>
                            <input type="text" id="bulkcity" name="bulkcity" class="form-control" placeholder="Please enter city">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label for="bulkrelocate">Willing to Relocate</label>
                            <select id="bulkrelocate" name="bulkrelocate" class="form-control" size="1">
                                <option value="">Please select</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label for="bulkcurrentctc">Current CTC</label>
                            <input type="text" id="bulkcurrentctc" name="bulkcurrentctc" class="form-control" placeholder="Please enter CTC">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label for="bulkexpectedctc">Expected CTC</label>
                            <input type="text" id="bulkexpectedctc" name="bulkexpectedctc" class="form-control" placeholder="Please enter CTC">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label for="sortbyinterval">Sort by</label>
                            <select id="sortbyinterval" name="sortbyinterval" class="form-control" size="1">
                                <option value="">Please select</option>
                                <option value="last-seven">Last 7 days</option>
                                <option value="last-thirty">Last 30 days</option>
                                <option value="last-month">Last 3 month</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-section">
                    <form action="" id="mailform" method="post" class="form-control-borderless">
                            <div class="mainform">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h2 class="text-center"><b>Send Email</b></h2>
                                    </div>
                                    <div class="col-md-3">
                                         <label for="lead-name">From</label>
                                    </div>
                                    <div class="col-md-9">
                                         <label for="lead-name">Digichefs HR<span class="text-danger">*</span></label><br><br>
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
                                                    <option>Select</option>
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
                                                <div class="occurance">No Data Selected</div>
                                                <input type="hidden" id="datasource" class="form-control" name="datasource">
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
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit"  class="btn btn-effect-ripple btn-primary">Send Bulk Email</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
             </div>
            </div>
           <table id="leaddatatable" class="table table-striped table-bordered table-vcenter">
            
                <thead>
                    <tr>
                        <th>Sr no.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone number</th>
                        <th>Roles</th>
                        <th>Experience</th>
                        <th>Date Applied</th>
                        <th>last contacted</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- END Datatables Block -->
</div>
<!-- END Page Contentt-->

                
<?php include 'inc/page_footer.php'; ?>
<?php include 'inc/template_scripts.php'; ?>

<!-- Load and execute javascript code used only in this page -->
<script src="js/pages/uiTables.js"></script>
<script src="js/bulkleads.js"></script>
<!--<script src="js/datatable/dataTables.buttons.min.js"></script>-->
<!--<script src="js/datatable/buttons.flash.min.js"></script>-->
<!--<script src="js/datatable/jszip.min.js"></script>-->
<!--<script src="js/datatable/pdfmake.min.js"></script>-->
<!--<script src="js/datatable/vfs_fonts.js"></script>-->

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


<?php include 'inc/template_end.php'; ?>
