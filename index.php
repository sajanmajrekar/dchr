<?php include 'inc/config.php'; $template['header_link'] = 'WELCOME'; ?>
<?php include 'inc/template_start.php'; ?>
<?php include 'inc/page_head.php'; ?>
<style>
    #assignedleads th:last-child, #assignedleads td:last-child{
        display:none;
    }
</style>
<!-- Page content -->
<div id="page-content">
    <!-- First Row -->
    <div class="row">
        <!-- Simple Stats Widgets -->
        <div class="col-sm-6 col-lg-3">
            <a href="javascript:void(0)" class="widget">
                <div class="widget-content widget-content-mini text-right clearfix">
                    <div class="widget-icon pull-left themed-background">
                        <i class="gi gi-cardio text-light-op"></i>
                    </div>
                    <h2 class="widget-heading h3">
                        <strong>
                          <?php   
                            $sql = "SELECT * from tblleads";
                            $result = $connect->query($sql);
                            if($result->num_rows > 0) { 
                            ?>
                            <span data-toggle="counter" data-to=<?php echo $result->num_rows;?>></span></strong>
                            <?php 
                            }else{
                                ?>
                             <span data-toggle="counter" data-to='0'></span></strong>
                            <?php
                                }
                            ?>

                    </h2>
                    <span class="text-muted">Total Candidate</span>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="javascript:void(0)" class="widget">
                <div class="widget-content widget-content-mini text-right clearfix">
                    <div class="widget-icon pull-left themed-background-success">
                        <i class="gi gi-user text-light-op"></i>
                    </div>
                    <h2 class="widget-heading h3 text-success">
                        <strong> <?php   
                            $sql = "SELECT * from tblstaff where admin!=2";
                            $result = $connect->query($sql);
                            if($result->num_rows > 0) { 
                            ?>
                            <span data-toggle="counter" data-to=<?php echo $result->num_rows;?>></span></strong>
                            <?php 
                            }else{
                                ?>
                             <span data-toggle="counter" data-to='0'></span></strong>
                            <?php
                                }
                            ?>
                    </h2>
                    <span class="text-muted">Users</span>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="javascript:void(0)" class="widget">
                <div class="widget-content widget-content-mini text-right clearfix">
                    <div class="widget-icon pull-left themed-background-warning">
                        <i class="gi gi-briefcase text-light-op"></i>
                    </div>
                    <h2 class="widget-heading h3 text-warning">
                        <strong><?php   
                            $sql = "SELECT * from tblleads where status=23";
                            $result = $connect->query($sql);
                            if($result->num_rows > 0) { 
                             ?>
                            <span data-toggle="counter" data-to=<?php echo $result->num_rows;?>></span></strong>
                            <?php 
                            }else{
                                ?>
                             <span data-toggle="counter" data-to='0'></span></strong>
                            <?php
                                }
                            ?>
                    </h2>
                    <span class="text-muted">Shortlisted</span>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3 hide">
            <a href="javascript:void(0)" class="widget">
                <div class="widget-content widget-content-mini text-right clearfix">
                    <div class="widget-icon pull-left themed-background-danger">
                        <i class="gi gi-wallet text-light-op"></i>
                    </div>
                    <h2 class="widget-heading h3 text-danger">
                        <strong>$ <span data-toggle="counter" data-to="5820"></span></strong>
                    </h2>
                    <span class="text-muted">EARNINGS</span>
                </div>
            </a>
        </div>
        <!-- END Simple Stats Widgets -->
    </div>
    <!-- END First Row -->
    <?php if($_SESSION['accounttype'] == 'admin' || $_SESSION['accounttype'] == 'superadmin'){ ?>
    <div class="block full">
                            <div class="block-title">
                                <h2>Lead Assigned</h2>
                            </div>
                            <div class="table-responsive">
                                <table id="assignedleads" class="table table-striped table-bordered table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;">Sr no.</th>
                                            <th>Name</th>
                                            <th>Access Type</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th style="width: 120px;">Leads assigned</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

    </div>
    <div class="row">
        <div class="col-lg-6">
                                <!-- Partial Responsive Block -->
            <div class="block full">
                <div class="block-title">
                                    <h2>Status Assigned</h2>
                                </div>
                                <div class="table-responsive">
                                <table id="statusleads" class="table table-striped table-bordered table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;">Sr no.</th>
                                            <th>Status</th>
                                            <th>Total Leads</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            </div>

        </div>
        <div class="col-lg-6">
                                <!-- Partial Responsive Block -->
            <div class="block full">
                <div class="block-title">
                                    <h2>Follow-up Leads</h2>
                                </div>
                                <div class="table-responsive">
                                <table id="Followuplead" class="table table-striped table-bordered table-vcenter">
                                    <thead>
                                        <tr>
                                            <th style="">Follow-up no.</th>
                                            <th >Total Follow-up</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
            </div>
            <div class="block full">
                <div class="block-title">
                                    <h2>Source Leads</h2>
                                </div>
                                <div class="table-responsive">
                                <table id="sourcelead" class="table table-striped table-bordered table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 50px;">Sr no.</th>
                                            <th>Source</th>
                                            <th>Total Leads</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
            </div>

        </div>
    </div>

  <?php  } ?>
<!-- END Page Content -->

<?php include 'inc/page_footer.php'; ?>
<?php include 'inc/template_scripts.php'; ?>

<!-- Load and execute javascript code used only in this page -->
<script src="js/pages/readyDashboard.js"></script>
<script src="js/dashboard.js"></script>
<script src="js/notif.js"></script>
notif.js
<script>$(function(){ ReadyDashboard.init(); });</script>
<?php include 'inc/template_end.php'; ?>