<?php
/**
 * config.php
 *
 * Author: pixelcave
 *
 * Configuration file. It contains variables used in the template as well as the primary navigation array from which the navigation is created
 *
 */

/* Template variables */

include("includes/dbcon.php");
require_once 'php_actions/core.php';
include("includes/function.php");

$gemini_resume_api_key = 'AIzaSyDRiWi91248Fu0fCdi2xypFqZteD7jcIRw';
$gemini_resume_model = 'gemini-3.5-flash';

$resume_tika_java_path = '';
$resume_tika_jar_path = '';

$localConfigPath = __DIR__ . DIRECTORY_SEPARATOR . 'local_config.php';
if (is_file($localConfigPath)) {
    include $localConfigPath;
}

$template = array(
    'name'              => 'Digichefs - CRM',
    'version'           => '2.9',
    'author'            => 'Digichefs',
    'robots'            => 'noindex, nofollow',
    'title'             => 'Digichefs - CRM',
    'description'       => 'Digichefs - CRM',
    // true                         enable page preloader
    // false                        disable page preloader
    'page_preloader'    => true,
    // 'navbar-default'             for a light header
    // 'navbar-inverse'             for a dark header
    'header_navbar'     => 'navbar-inverse',
    // ''                           empty for a static header/main sidebar
    // 'navbar-fixed-top'           for a top fixed header/sidebars
    // 'navbar-fixed-bottom'        for a bottom fixed header/sidebars
    'header'            => 'navbar-fixed-top',
    // ''                           empty for the default full width layout
    // 'fixed-width'                for a fixed width layout (can only be used with a static header/main sidebar)
    'layout'            => '',
    // 'sidebar-visible-lg-mini'    main sidebar condensed - Mini Navigation (> 991px)
    // 'sidebar-visible-lg-full'    main sidebar full - Full Navigation (> 991px)
    // 'sidebar-alt-visible-lg'     alternative sidebar visible by default (> 991px) (You can add it along with another class - leaving a space between)
    // 'sidebar-light'              for a light main sidebar (You can add it along with another class - leaving a space between)
    'sidebar'           => 'sidebar-visible-lg-full enable-cookies',
    // ''                           Disable cookies (best for setting an active color theme from the next variable)
    // 'enable-cookies'             Enables cookies for remembering active color theme when changed from the sidebar links (the next color theme variable will be ignored)
    //'enable-cookies' => true,
    'cookies'           => '',
    // '', 'classy', 'social', 'flat', 'amethyst', 'creme', 'passion'
    'theme'             => '',
    // Used as the text for the header link - You can set a value in each page if you like to enable it in the header
    'header_link'       => '',
    // The name of the files in the inc/ folder to be included in page_head.php - Can be changed per page if you
    // would like to have a different file included (eg a different alternative sidebar)
    'inc_sidebar'       => 'page_sidebar',
    'inc_sidebar_alt'   => 'page_sidebar_alt',
    'inc_header'        => 'page_header',
    // The following variable is used for setting the active link in the sidebar menu
    'active_page'       => basename($_SERVER['PHP_SELF'])
);

/* Primary navigation array (the primary navigation will be created automatically based on this array, up to 3 levels deep) */
if( isset($_SESSION['accounttype'])){
    if($_SESSION['accounttype'] !="user"){
$primary_nav = array(
    array(
        'name'  => 'Dashboard',
        'url'   => 'index.php',
        'icon'  => 'gi gi-compass',
        'type'  => true
    ),
    array(
        'url'   => 'separator',
        'type'  => true
    ),
     array(
        'name'  => 'User',
        'icon'  => 'fa fa-user',
        'url'   => 'users.php',
        'type'  => $_SESSION['accounttype'] == 'user'  ? false : true
    ),
     array(
        'name'  => 'Candidate Source',
        'icon'  => 'fa fa-user',
        'url'   => 'source.php',
        'type'  => $_SESSION['accounttype'] == 'user'  ? false : true
    ),
     array(
        'name'  => 'Candidate Roles',
        'icon'  => 'fa fa-user',
        'url'   => 'role.php',
        'type'  => $_SESSION['accounttype'] == 'user'  ? false : true
    ),
     array(
        'name'  => 'Candidate Status',
        'icon'  => 'fa fa-status',
        'url'   => 'status.php',
        'type'  => $_SESSION['accounttype'] == 'user'  ? false : true
    ),
     array(
                'name'  => 'Candidates',
                'icon'  => 'fa fa-globe',
                'url'   => 'candidates.php',
                'type'  => true,
            ),
     array(
        'name'  => 'Resume Intelligence',
        'icon'  => 'fa fa-file-text-o',
        'type'  => true,
        'sub'   => array(
            array(
                'name'  => 'Worker Monitor',
                'url'   => 'resume_intelligence.php',
                'type'  => true
            ),
            array(
                'name'  => 'Resume Library',
                'url'   => 'resume_library.php',
                'type'  => true
            ),
            array(
                'name'  => 'Careers Imports',
                'url'   => 'careers_imports.php',
                'type'  => true
            )
        )
    ),
     array(
        'name'  => 'Bulk Email',
        'icon'  => 'fa fa-envelope-o',
        'type'  => true,
        'sub'   => array(
            /*array(
                'name'  => 'All leads',
                'url'   => 'leads.php',
                'type'  => $_SESSION['accounttype'] == 'user'  ? false : true
            ),*/
            array(
                'name'  => 'Send Email',
                'url'   => 'bulkmail.php',
                'type'  => true,
            ),
             array(
                'name'  => 'Email log',
                'url'   => 'emailogs.php',
                'type'  => true,
            )
        )
    )
    //  array(
    //     'name'  => 'Leads',
    //     'icon'  => 'fa fa-globe',
    //     'type'  => true,
    //     'sub'   => array(
    //         array(
    //             'name'  => 'All leads',
    //             'url'   => 'leads.php',
    //             'type'  => $_SESSION['accounttype'] == 'user'  ? false : true
    //         ),
    //         array(
    //             'name'  => 'Candidates',
    //             'url'   => 'candidates.php',
    //             'type'  => true,
    //         )
    //     )
    // )
);
}else{
    $primary_nav = array(
    array(
        'name'  => 'Dashboard',
        'url'   => 'index.php',
        'icon'  => 'gi gi-compass',
        'type'  => true
    ),
    array(
        'url'   => 'separator',
        'type'  => true
    ),
     array(
        'name'  => 'User',
        'icon'  => 'fa fa-user',
        'url'   => 'users.php',
        'type'  => $_SESSION['accounttype'] == 'user'  ? false : true
    ),
     array(
        'name'  => 'Lead Source',
        'icon'  => 'fa fa-user',
        'url'   => 'source.php',
        'type'  => $_SESSION['accounttype'] == 'user'  ? false : true
    ),
     array(
        'name'  => 'Lead Status',
        'icon'  => 'fa fa-status',
        'url'   => 'status.php',
        'type'  => $_SESSION['accounttype'] == 'user'  ? false : true
    ),
     array(
        'name'  => 'Leads',
        'icon'  => 'fa fa-globe',
        'type'  => true,
        'sub'   => array(
            /*array(
                'name'  => 'All leads',
                'url'   => 'leads.php',
                'type'  => $_SESSION['accounttype'] == 'user'  ? false : true
            ),*/
            array(
                'name'  => 'My Leads',
                'url'   => 'my-leads.php',
                'type'  => true,
            )
        )
    ),
     array(
        'name'  => 'Resume Intelligence',
        'icon'  => 'fa fa-file-text-o',
        'type'  => true,
        'sub'   => array(
            array(
                'name'  => 'Worker Monitor',
                'url'   => 'resume_intelligence.php',
                'type'  => true
            ),
            array(
                'name'  => 'Resume Library',
                'url'   => 'resume_library.php',
                'type'  => true
            ),
            array(
                'name'  => 'Careers Imports',
                'url'   => 'careers_imports.php',
                'type'  => true
            )
        )
    ),
     array(
        'name'  => 'Bulk Email',
        'icon'  => 'fa fa-globe',
        'type'  => true,
        'sub'   => array(
            /*array(
                'name'  => 'All leads',
                'url'   => 'leads.php',
                'type'  => $_SESSION['accounttype'] == 'user'  ? false : true
            ),*/
            array(
                'name'  => 'Send Email',
                'url'   => 'bulkmail.php',
                'type'  => true,
            ),
             array(
                'name'  => 'Email log',
                'url'   => 'emailogs.php',
                'type'  => true,
            )
        )
    )
);
}
}
