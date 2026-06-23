<?php
/**
 * page_head.php
 *
 * Author: pixelcave
 *
 * The head of each page
 *
 */

$sessionUser = isset($_SESSION['user']) ? $_SESSION['user'] : '';
$sessionId = isset($_SESSION['id']) ? $_SESSION['id'] : '';
$sessionAccountType = isset($_SESSION['accounttype']) ? $_SESSION['accounttype'] : '';

if ($sessionUser === '' || $sessionId === '') {
    session_destroy();
    echo '<script>window.location.href="login.php";</script>';
    exit();
}

$query = "select * from tblstaff where email='" . $sessionUser . "' and staffid='" . $sessionId . "'";
$result = query($query);
$rowcount = mysqli_num_rows($result);

if ($rowcount == 0) {
    session_destroy();
    echo '<script>window.location.href="login.php";</script>';
    exit();
}

$email = $sessionUser;
$isAdmin = false;
if ($sessionAccountType == 'admin') {
    $isAdmin = true;
}

$sql = query("select * from tblstaff where email='$email'");
$name = '';
$rowcount = mysqli_num_rows($sql);
if ($rowcount != 0) {
    $r = $sql->fetch_object();
    $name = $r->firstname;
}
?>

<!-- Page Wrapper -->
<!-- In the PHP version you can set the following options from inc/config file -->
<!--
    Available classes:

    'page-loading'      enables page preloader
-->
<div id="page-wrapper"<?php if ($template['page_preloader']) { echo ' class="page-loading"'; } ?>>
    <!-- Preloader -->
    <!-- Preloader functionality (initialized in js/app.js) - pageLoading() -->
    <!-- Used only if page preloader enabled from inc/config (PHP version) or the class 'page-loading' is added in #page-wrapper element (HTML version) -->
    <div class="preloader">
        <div class="inner">
            <!-- Animation spinner for all modern browsers -->
            <div class="preloader-spinner themed-background hidden-lt-ie10"></div>

            <!-- Text for IE9 -->
            <h3 class="text-primary visible-lt-ie10"><strong>Loading..</strong></h3>
        </div>
    </div>
    <!-- END Preloader -->

    <!-- Page Container -->
    <!-- In the PHP version you can set the following options from inc/config file -->
    <!--
        Available #page-container classes:

        'sidebar-light'                                 for a light main sidebar (You can add it along with any other class)

        'sidebar-visible-lg-mini'                       main sidebar condensed - Mini Navigation (> 991px)
        'sidebar-visible-lg-full'                       main sidebar full - Full Navigation (> 991px)

        'sidebar-alt-visible-lg'                        alternative sidebar visible by default (> 991px) (You can add it along with any other class)

        'header-fixed-top'                              has to be added only if the class 'navbar-fixed-top' was added on header.navbar
        'header-fixed-bottom'                           has to be added only if the class 'navbar-fixed-bottom' was added on header.navbar

        'fixed-width'                                   for a fixed width layout (can only be used with a static header/main sidebar layout)

        'enable-cookies'                                enables cookies for remembering active color theme when changed from the sidebar links (You can add it along with any other class)
    -->
    <?php
        $page_classes = '';

        if ($template['header'] == 'navbar-fixed-top') {
            $page_classes = 'header-fixed-top';
        } else if ($template['header'] == 'navbar-fixed-bottom') {
            $page_classes = 'header-fixed-bottom';
        }

        if ($template['sidebar']) {
            $page_classes .= (($page_classes == '') ? '' : ' ') . $template['sidebar'];
        }

        if ($template['layout'] == 'fixed-width' && $template['header'] == '') {
            $page_classes .= (($page_classes == '') ? '' : ' ') . $template['layout'];
        }

        if ($template['cookies'] === 'enable-cookies') {
            $page_classes .= (($page_classes == '') ? '' : ' ') . $template['cookies'];
        }
    ?>
    <div id="page-container"<?php if ($page_classes) { echo ' class="' . $page_classes . '"'; } ?>>
        <?php if ($template['inc_sidebar_alt']) { include 'inc/' . $template['inc_sidebar_alt'] . '.php'; } ?>
        <?php if ($template['inc_sidebar']) { include 'inc/' . $template['inc_sidebar'] . '.php'; } ?>

        <!-- Main Container -->
        <div id="main-container">
            <?php if ($template['inc_header']) { include 'inc/' . $template['inc_header'] . '.php'; } ?>
