<?php include 'inc/config.php'; $template['header_link'] = 'PAGES'; ?>
<?php include 'inc/template_start.php'; ?>
<?php include 'inc/page_head.php'; ?>

<!-- Page content -->
<div id="page-content">
    <!-- Forum Header -->
    <div class="content-header">
        <div class="row">
            <div class="col-sm-6">
                <div class="header-section">
                    <h1>Forum</h1>
                </div>
            </div>
            <div class="col-sm-6 hidden-xs">
                <div class="header-section">
                    <ul class="breadcrumb breadcrumb-top">
                        <li>Extra Pages</li>
                        <li><a href="">Forum</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- END Forum Header -->

    <!-- Forum Block -->
    <div class="block">
        <!-- Forum Tabs Title -->
        <div class="block-title">
            <ul class="nav nav-tabs" data-toggle="tabs">
                <li class="active"><a href="#forum-categories">Categories</a></li>
                <li><a href="#forum-topics">Topics</a></li>
                <li><a href="#forum-discussion">Discussion</a></li>
            </ul>
        </div>
        <!-- END Forum Tabs -->

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Forum -->
            <div class="tab-pane active" id="forum-categories">
                <!-- Intro Category -->
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th colspan="2">Introduction</th>
                            <th class="text-center hidden-xs hidden-sm" style="width: 100px;">Topics</th>
                            <th class="text-center hidden-xs hidden-sm" style="width: 100px;">Posts</th>
                            <th class="hidden-xs hidden-sm" style="width: 200px;">Last Post</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" style="width: 75px;"><i class="gi gi-globe fa-2x text-muted text-muted"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Welcome</strong></a>
                                </h4>
                                <div class="text-muted">Feel free to come inside and introduce yourself!</div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>205</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>1265</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Sandra Gilbert</a><br><small>February 30, 2015</small></td>
                        </tr>
                        <tr>
                            <td class="text-center"><i class="gi gi-bullhorn fa-2x text-muted"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Announcements</strong></a>
                                </h4>
                                <div class="text-muted">For the latest news, this is the place</div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>251</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>6352</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Carolyn Cruz</a><br><small>June 15, 2015</small></td>
                        </tr>
                        <tr>
                            <td class="text-center"><i class="gi gi-pen fa-2x text-muted"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Terms &amp; Conditions</strong></a>
                                </h4>
                                <div class="text-muted">Please read the rules of our forum</div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>20</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>20</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Shirley Ray</a><br><small>February 10, 2015</small></td>
                        </tr>
                    </tbody>
                </table>
                <!-- END Intro Category -->

                <!-- Web Application Category -->
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th colspan="2">Web Application</th>
                            <th class="text-center hidden-xs hidden-sm" style="width: 100px;">Topics</th>
                            <th class="text-center hidden-xs hidden-sm" style="width: 100px;">Posts</th>
                            <th class="hidden-xs hidden-sm" style="width: 200px;">Last Post</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" style="width: 75px;"><i class="gi gi-brush fa-2x text-muted"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Getting Started</strong></a>
                                </h4>
                                <div class="text-muted">New user? Don't worry, you will find everything you need in here</div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>120</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>1253</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Harry Owens</a><br><small>April 12, 2015</small></td>
                        </tr>
                        <tr>
                            <td class="text-center"><i class="gi gi-pencil fa-2x text-muted"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Tutorials</strong></a>
                                </h4>
                                <div class="text-muted">The best place for tutorials</div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>302</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>1356</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Edward Cook</a><br><small>March 15, 2015</small></td>
                        </tr>
                        <tr>
                            <td class="text-center"><i class="gi gi-embed_close fa-2x text-muted"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Plugins</strong></a>
                                </h4>
                                <div class="text-muted">Find out the best of the best out there</div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>365</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>6330</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Samuel Collins</a><br><small>February 30, 2015</small></td>
                        </tr>
                        <tr>
                            <td class="text-center"><i class="gi gi-tower fa-2x text-muted"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>API</strong></a>
                                </h4>
                                <div class="text-muted">API related discussions</div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>862</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>7650</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Carl Chen</a><br><small>March 24, 2015</small></td>
                        </tr>
                        <tr>
                            <td class="text-center"><i class="gi gi-heat fa-2x text-muted"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Extentions</strong></a>
                                </h4>
                                <div class="text-muted">Extend our web application's features</div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>80</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>658</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Justin Weber</a><br><small>February 15, 2015</small></td>
                        </tr>
                    </tbody>
                </table>
                <!-- END Web Application Category -->

                <!-- Support Category -->
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th colspan="2">Support</th>
                            <th class="text-center hidden-xs hidden-sm" style="width: 100px;">Topics</th>
                            <th class="text-center hidden-xs hidden-sm" style="width: 100px;">Posts</th>
                            <th class="hidden-xs hidden-sm" style="width: 200px;">Last Post</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" style="width: 75px;"><i class="gi gi-book fa-2x text-muted"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>How to</strong></a>
                                </h4>
                                <div class="text-muted">The best guides handcrafted just for you!</div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>120</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>1985</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Judith Smith</a><br><small>January 20, 2015</small></td>
                        </tr>
                        <tr>
                            <td class="text-center"><i class="gi gi-circle_question_mark fa-2x text-muted"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Questions</strong></a>
                                </h4>
                                <div class="text-muted">Need assistance? Don’t worry, we are here to help!</div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>2150</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>12500</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Roger Griffin</a><br><small>January 25, 2015</small></td>
                        </tr>
                        <tr>
                            <td class="text-center"><i class="gi gi-book_open fa-2x text-muted"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Users’ Sandbox</strong></a>
                                </h4>
                                <div class="text-muted">Discuss with other users</div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>2169</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>352</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Megan Harvey</a><br><small>May 2, 2015</small></td>
                        </tr>
                    </tbody>
                </table>
                <!-- END Support Category -->
            </div>
            <!-- END Forum -->

            <!-- Topics -->
            <div class="tab-pane" id="forum-topics">
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th colspan="2">Getting Started</th>
                            <th class="text-center hidden-xs hidden-sm" style="width: 100px;">Replies</th>
                            <th class="text-center hidden-xs hidden-sm" style="width: 100px;">Views</th>
                            <th class="hidden-xs hidden-sm" style="width: 200px;">Last Post</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" style="width: 75px;"><i class="gi gi-pin_flag fa-2x text-danger"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Welcome to our Getting Started category</strong></a>
                                </h4>
                                <div class="text-muted"><a href="page_ready_profile.php">Hannah Bates</a> on <em>January 1, 2015</em></div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>456</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>6598</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Maria Young</a><br><small>June 25, 2015</small></td>
                        </tr>
                        <tr>
                            <td class="text-center"><i class="gi gi-pin_flag fa-2x text-danger"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>A big upgrade is coming to our app</strong></a>
                                </h4>
                                <div class="text-muted"><a href="page_ready_profile.php">Anna Matthews</a> on <em>February 2, 2015</em></div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>277</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>1254</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Jacob Matthews</a><br><small>June 28, 2015</small></td>
                        </tr>
                        <tr>
                            <td class="text-center"><i class="gi gi-pin_flag fa-2x text-danger"></i></td>
                            <td>
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Tips &amp; tricks for staying motivated</strong></a>
                                </h4>
                                <div class="text-muted"><a href="page_ready_profile.php">Jeremy Davis</a> on <em>February 5, 2015</em></div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>248</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>7548</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Catherine Carr</a><br><small>June 30, 2015</small></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>2015, all the new features!</strong></a>
                                </h4>
                                <div class="text-muted"><a href="page_ready_profile.php">Anthony Carroll</a> on <em>January 3, 2015</em></div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>247</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>6325</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Mary Fields</a><br><small>July 10, 2015</small></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Your app saved me tons of time</strong></a>
                                </h4>
                                <div class="text-muted"><a href="page_ready_profile.php">Albert Barnes</a> on <em>February 10, 2015</em>text</div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>247</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>2589</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Roy Moreno</a><br><small>March 13, 2015</small></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Is it easy to upgrade my plan?</strong></a>
                                </h4>
                                <div class="text-muted"><a href="page_ready_profile.php">Bruce Carlson</a> on <em>January 6, 2015</em></div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>247</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>4587</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Juan Shaw</a><br><small>April 23, 2015</small></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Check out all those tips &amp; tricks!</strong></a>
                                </h4>
                                <div class="text-muted"><a href="page_ready_profile.php">Andrew Ross</a> on <em>February 8, 2015</em></div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>528</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>1254</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Alan Weaver</a><br><small>February 17, 2015</small></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Review needed</strong></a>
                                </h4>
                                <div class="text-muted"><a href="page_ready_profile.php">Jerry Vasquez</a> on <em>January 1, 2015</em></div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>584</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>1478</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Juan Carroll</a><br><small>June 6, 2015</small></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Testing out the API</strong></a>
                                </h4>
                                <div class="text-muted"><a href="page_ready_profile.php">Jeremy Miller</a> on <em>January 1, 2015</em></div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>541</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>9512</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Philip Evans</a><br><small>June 10, 2015</small></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Issue when saving a file, can you help me?</strong></a>
                                </h4>
                                <div class="text-muted"><a href="page_ready_profile.php">Sean Jones</a> on <em>January 1, 2015</em></div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>158</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>6325</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Jesse Gray</a><br><small>May 26, 2015</small></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>New Features!</strong></a>
                                </h4>
                                <div class="text-muted"><a href="page_ready_profile.php">Denise Butler</a> on <em>January 1, 2015</em></div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>147</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>1258</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Willie Lane</a><br><small>March 9, 2015</small></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Question about the new features!</strong></a>
                                </h4>
                                <div class="text-muted"><a href="page_ready_profile.php">Julie Banks</a> on <em>January 1, 2015</em></div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>264</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>6325</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Nicholas Lee</a><br><small>April 8, 2015</small></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <h4>
                                    <a href="javascript:void(0)" class="text-dark"><strong>Which plan to choose?</strong></a>
                                </h4>
                                <div class="text-muted"><a href="page_ready_profile.php">Anna Kim</a> on <em>January 1, 2015</em></div>
                            </td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>156</strong></a></td>
                            <td class="text-center hidden-xs hidden-sm"><a href="javascript:void(0)"><strong>9685</strong></a></td>
                            <td class="hidden-xs hidden-sm">by <a href="page_ready_profile.php">Donna Ford</a><br><small>April 2, 2015</small></td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-center">
                    <ul class="pagination">
                        <li class="disabled"><a href="javascript:void(0)">Prev</a></li>
                        <li class="active"><a href="javascript:void(0)">1</a></li>
                        <li><a href="javascript:void(0)">2</a></li>
                        <li><a href="javascript:void(0)">3</a></li>
                        <li><a href="javascript:void(0)">...</a></li>
                        <li><a href="javascript:void(0)">999</a></li>
                        <li><a href="javascript:void(0)">Next</a></li>
                    </ul>
                </div>
            </div>
            <!-- END Topics -->

            <!-- Discussion -->
            <div class="tab-pane" id="forum-discussion">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th colspan="2">All the new Features <small>by <a href="page_ready_profile.php"><strong>Randy Sanders</strong></a> on <em>May 1, 2015</em></small></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center"><a href="page_ready_profile.php"><strong>User</strong></a></td>
                            <td>on <em>May 10, 2015 - 12:00</em></td>
                        </tr>
                        <tr>
                            <td class="text-center" style="width: 12%;">
                                <div class="push-bit">
                                    <a href="page_ready_profile.php">
                                        <img src="img/placeholders/avatars/avatar<?php echo rand(1, 16); ?>.jpg" alt="avatar" class="img-circle">
                                    </a>
                                </div>
                                <small>1253 Posts</small>
                            </td>
                            <td>
                                <p>Donec lacinia venenatis metus at bibendum? In hac habitasse platea dictumst. Proin ac nibh rutrum lectus rhoncus eleifend. Sed porttitor pretium venenatis. Suspendisse potenti. Aliquam quis ligula elit. Aliquam at orci ac neque semper dictum. Sed tincidunt scelerisque ligula, et facilisis nulla hendrerit non. Suspendisse potenti. Pellentesque non accumsan orci. Praesent at lacinia dolor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec lacinia venenatis metus at bibendum? In hac habitasse platea dictumst. Proin ac nibh rutrum lectus rhoncus eleifend. Sed porttitor pretium venenatis. Suspendisse potenti. Aliquam quis ligula elit. Aliquam at orci ac neque semper dictum. Sed tincidunt scelerisque ligula, et facilisis nulla hendrerit non. Suspendisse potenti. Pellentesque non accumsan orci. Praesent at lacinia dolor. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                <em>User’s Signature</em>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center"><a href="page_ready_profile.php"><strong>User</strong></a></td>
                            <td>on <em>May 25, 2015 - 19:30</em></td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <div class="push-bit">
                                    <a href="page_ready_profile.php">
                                        <img src="img/placeholders/avatars/avatar<?php echo rand(1, 16); ?>.jpg" alt="avatar" class="img-circle">
                                    </a>
                                </div>
                                <small>5213 Posts</small>
                            </td>
                            <td>
                                <p>Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum. Donec vitae ultrices dolor?</p>
                                <em>User’s Signature</em>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center"><a href="page_ready_profile.php"><strong>User</strong></a></td>
                            <td>on <em>June 1, 2015 - 16:01</em></td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <div class="push-bit">
                                    <a href="page_ready_profile.php">
                                        <img src="img/placeholders/avatars/avatar<?php echo rand(1, 16); ?>.jpg" alt="avatar" class="img-circle">
                                    </a>
                                </div>
                                <small>2585 Posts</small>
                            </td>
                            <td>
                                <p>Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum. Donec vitae ultrices dolor?</p>
                                <p>Donec lacinia venenatis metus at bibendum? In hac habitasse platea dictumst. Proin ac nibh rutrum lectus rhoncus eleifend. Sed porttitor pretium venenatis. Suspendisse potenti. Aliquam quis ligula elit. Aliquam at orci ac neque semper dictum. Sed tincidunt scelerisque ligula, et facilisis nulla hendrerit non. Suspendisse potenti. Pellentesque non accumsan orci. Praesent at lacinia dolor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec lacinia venenatis metus at bibendum? In hac habitasse platea dictumst. Proin ac nibh rutrum lectus rhoncus eleifend. Sed porttitor pretium venenatis. Suspendisse potenti. Aliquam quis ligula elit. Aliquam at orci ac neque semper dictum. Sed tincidunt scelerisque ligula, et facilisis nulla hendrerit non. Suspendisse potenti. Pellentesque non accumsan orci. Praesent at lacinia dolor. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                <em>User’s Signature</em>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center"><a href="page_ready_profile.php"><strong>User</strong></a></td>
                            <td>on <em>June 10, 2015 - 09:16</em></td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <div class="push-bit">
                                    <a href="page_ready_profile.php">
                                        <img src="img/placeholders/avatars/avatar<?php echo rand(1, 16); ?>.jpg" alt="avatar" class="img-circle">
                                    </a>
                                </div>
                                <small>3251 Posts</small>
                            </td>
                            <td>
                                <p>Donec lacinia venenatis metus at bibendum? In hac habitasse platea dictumst. Proin ac nibh rutrum lectus rhoncus eleifend. Sed porttitor pretium venenatis. Suspendisse potenti. Aliquam quis ligula elit. Aliquam at orci ac neque semper dictum. Sed tincidunt scelerisque ligula, et facilisis nulla hendrerit non. Suspendisse potenti. Pellentesque non accumsan orci. Praesent at lacinia dolor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec lacinia venenatis metus at bibendum? In hac habitasse platea dictumst. Proin ac nibh rutrum lectus rhoncus eleifend. Sed porttitor pretium venenatis. Suspendisse potenti. Aliquam quis ligula elit. Aliquam at orci ac neque semper dictum. Sed tincidunt scelerisque ligula, et facilisis nulla hendrerit non. Suspendisse potenti. Pellentesque non accumsan orci. Praesent at lacinia dolor. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                <p>Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum. Donec vitae ultrices dolor?</p>
                                <em>User’s Signature</em>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center"><a href="page_ready_profile.php"><strong>User</strong></a></td>
                            <td>on <em>June 15, 2015 - 15:15</em></td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <div class="push-bit">
                                    <a href="page_ready_profile.php">
                                        <img src="img/placeholders/avatars/avatar<?php echo rand(1, 16); ?>.jpg" alt="avatar" class="img-circle">
                                    </a>
                                </div>
                                <small>5621 Posts</small>
                            </td>
                            <td>
                                <p>Mauris tincidunt tincidunt turpis in porta. Integer fermentum tincidunt auctor. Vestibulum ullamcorper, odio sed rhoncus imperdiet, enim elit sollicitudin orci, eget dictum leo mi nec lectus. Nam commodo turpis id lectus scelerisque vulputate. Integer sed dolor erat. Fusce erat ipsum, varius vel euismod sed, tristique et lectus? Etiam egestas fringilla enim, id convallis lectus laoreet at. Fusce purus nisi, gravida sed consectetur ut, interdum quis nisi. Quisque egestas nisl id lectus facilisis scelerisque? Proin rhoncus dui at ligula vestibulum ut facilisis ante sodales! Suspendisse potenti. Aliquam tincidunt sollicitudin sem nec ultrices. Sed at mi velit. Ut egestas tempor est, in cursus enim venenatis eget! Nulla quis ligula ipsum. Donec vitae ultrices dolor?</p>
                                <em>User’s Signature</em>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center"><a href="page_ready_profile.php"><strong>You</strong></a></td>
                            <td><em>Now</em></td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <a href="page_ready_profile.php"><img src="img/placeholders/avatars/avatar.jpg" class="img-circle" alt="avatar"></a>
                            </td>
                            <td>
                                <form action="page_ready_forum.php" method="post" class="form-horizontal" onsubmit="return false;">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <textarea id="textarea-message" name="textarea-message" rows="8" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-reply"></i> Reply</button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-center">
                    <ul class="pagination">
                        <li class="disabled"><a href="javascript:void(0)">Prev</a></li>
                        <li class="active"><a href="javascript:void(0)">1</a></li>
                        <li><a href="javascript:void(0)">2</a></li>
                        <li><a href="javascript:void(0)">3</a></li>
                        <li><a href="javascript:void(0)">...</a></li>
                        <li><a href="javascript:void(0)">10</a></li>
                        <li><a href="javascript:void(0)">Next</a></li>
                    </ul>
                </div>
            </div>
            <!-- END Discussion -->
        </div>
        <!-- END Tab Content -->
    </div>
    <!-- END Forum Block -->
</div>
<!-- END Page Content -->

<?php include 'inc/page_footer.php'; ?>
<?php include 'inc/template_scripts.php'; ?>
<?php include 'inc/template_end.php'; ?>