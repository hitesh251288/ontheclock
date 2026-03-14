<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//include "cached_header.php";
$pageKey = basename($_SERVER['PHP_SELF']);
$username = $_SESSION[$session_variable . "username"];

if (isset($_SESSION['cached_header'][$username][$pageKey])) {
    echo $_SESSION['cached_header'][$username][$pageKey];
    return;
}

// Start output buffering
ob_start();
$currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="robots" content="noindex,nofollow" />
        <title>On The Clock</title>
        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png" />
        <link href="dist/css/style.min.css" rel="stylesheet" />
        <link href="dist/css/select2.min.css?v=<?= time(); ?>" rel="stylesheet" />
        
        <!-- Custom CSS -->
        <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">-->
<!--        <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" />
        <link rel="stylesheet" href="dist/css/dataTables.dataTables.css" />-->
        
        <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" onerror="this.onerror=null;this.href='dist/css/select2.min.css';" />-->
        <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" rel="stylesheet" onerror="this.onerror=null;this.href='assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.css';" />
        <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet" onerror="this.onerror=null;this.href='dist/css/dataTables.dataTables.css';" />

        <?php if($currentPage == 'Dashboard.php'){ ?>
            <link href="assets/libs/flot/css/float-chart.css" rel="stylesheet" />
        <?php } ?>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="////oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!--<script src="assets/libs/jquery/dist/jquery.min.js"></script>-->
        <style>
            .topbar {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                /* Ensures the header is on top of other content */
            }
            .page-wrapper{
                margin-top: 4% !important;
            }
            #main-wrapper.closed-sidebar .left-sidebar {
                display: none !important;
            }

            #main-wrapper.closed-sidebar {
                padding-left: 0 !important; /* optional if your layout pushes content */
            }
            table.dataTable td, 
            table.dataTable th {
                padding: 2px 4px !important; /* adjust values as needed */
                font-size: 13px; /* optional: reduce font size for compactness */
            }
            .topbar .mailbox, .topbar .user-dd {
                min-width: 0px !important;
            }
        </style>
    </head>
    <body>
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
<!--                <div class="preloader">
                    <div class="lds-ripple">
                        <div class="lds-pos"></div>
                        <div class="lds-pos"></div>
                    </div>
                </div>-->
        <!-- ============================================================== -->
        <!-- Main wrapper - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
            <!-- ============================================================== -->
            <!-- Topbar header - style you can find in pages.scss -->
            <!-- ============================================================== -->
            <header class="topbar" data-navbarbg="skin5">
                <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                    <div class="navbar-header" data-logobg="skin5">
                        <a class="navbar-brand" href="Dashboard.php">
                            <!-- Logo icon -->
                            <b class="logo-icon ps-2">
                                <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                                <!-- Dark Logo icon -->
<!--                                <img
                                    src="assets/images/logo-icon.png"
                                    alt="homepage"
                                    class="light-logo"
                                    width="25"
                                    />-->
                            </b>
                            <!--End Logo icon -->
                            <!-- Logo text -->
                            <span class="logo-text ms-2">
                                <!-- dark Logo text -->
                                <img src="img/logo.png" alt="homepage" loading="lazy" class="light-logo" style="height: 60px;width:95%"/>
                            </span>
                            <!-- Logo icon -->
                            <!-- <b class="logo-icon"> -->
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                            <!-- <img src="assets/images/logo-text.png" alt="homepage" class="light-logo" /> -->

                            <!-- </b> -->
                            <!--End Logo icon -->
                        </a>
                        <!-- ============================================================== -->
                        <!-- End Logo -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- Toggle which is visible on mobile only -->
                        <!-- ============================================================== -->
                        <a
                            class="nav-toggler waves-effect waves-light d-block d-md-none"
                            href="javascript:void(0)"
                            ><i class="ti-menu ti-close"></i
                            ></a>
                    </div>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
                        <!-- ============================================================== -->
                        <!-- toggle and nav items -->
                        <!-- ============================================================== -->
                        <ul class="navbar-nav float-start me-auto">
                            <!--sidebartoggler-->
                            <!--min-sidebar-->
                            <li class="nav-item d-none d-lg-block sidebartoggler"><a class="nav-link waves-effect waves-light" href="javascript:void(0)" data-sidebartype="closed-sidebar"><i class="mdi mdi-menu font-24"></i></a></li>
                            <li>
                                <?php
                                if ($excel != "yes") {
                                    displayHeader($prints, false, false);
                                }
                                ?>
                            </li>
                            <!-- ============================================================== -->
                            <!-- create new -->
                            <!-- ============================================================== -->

                            <!-- ============================================================== -->
                            <!-- Search -->
                            <!-- ============================================================== -->
                            <!--                            <li class="nav-item search-box">
                                                            <form class="app-search position-absolute">
                                                                <input type="text" class="form-control" placeholder="Search &amp; enter"/>
                                                                <a class="srh-btn"><i class="mdi mdi-window-close"></i></a>
                                                            </form>
                                                        </li>-->
                        </ul>                        
                        <!-- ============================================================== -->
                        <!-- Right side toggle and nav items -->
                        <!-- ============================================================== -->
                        <ul class="navbar-nav float-end">
                            <li class="nav-item">
                                <?php
                                if ($excel != "yes") {
//                                    displayHeader($prints, false, false);
                                }
                                ?>
                            </li>
                            <li>
                                <?php
                                $conn = openConnection();
                                $query = "SELECT ClientLogo FROM OtherSettingMaster";
                                $result = selectData($conn, $query);
                                if (isset($result) && $result[0] != '') {
                                    $companyImage = $result[0];
                                } else {
                                    $companyImage = "noimage.jpg";
                                }
                                ?>
                                <img src="img/<?php echo $companyImage; ?>" loading="lazy" class="loginImg" style="height:70px;">
                            </li>
                            <!-- ============================================================== -->
                            <!-- End Messages -->
                            <!-- ============================================================== -->

                            <!-- ============================================================== -->
                            <!-- User profile and search -->
                            <!-- ============================================================== -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <!--<img src="assets/images/users/logout.png" alt="user" class="rounded-circle" width="50"/>-->
                                    <img src="assets/images/users/1.jpg" alt="user" class="rounded-circle" width="31" loading="lazy">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end user-dd animated" aria-labelledby="navbarDropdown">
                                    <div class="dropdown-divider"></div>
                                    <?php // echo helpVerInfo(); ?>
                                    <a class="dropdown-item" href="Password.php"><i class="fa fa-key me-1 ms-1"></i> Change Password</a>
                                    <a class="dropdown-item" href="TaskMaster.php"><i class="fa fa-tasks me-1 ms-1"></i> Task</a>
                                    <a class="dropdown-item" href="About.php"><i class="fa fa-info-circle me-1 ms-1"></i> About Us</a>
                                    <a class="dropdown-item" href="Login.php?act=signout"><i class="fa fa-power-off me-1 ms-1"></i> Logout</a>
                                </ul>
                            </li>
                            <!-- ============================================================== -->
                            <!-- User profile and search -->
                            <!-- ============================================================== -->
                        </ul>
                    </div>
                </nav>
            </header>
            <!-- ============================================================== -->
            <!-- End Topbar header -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Left Sidebar - style you can find in sidebar.scss  -->
            <!-- ============================================================== -->
            <?php echo displayLinks($current_module, $userlevel); ?>
            <!-- ============================================================== -->
            <!-- End Left Sidebar - style you can find in sidebar.scss  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Page wrapper  -->
            <!-- ============================================================== -->
            <div class="page-wrapper">
                <!-- ============================================================== -->
                <!-- Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
<?php
// Cache and output
$_SESSION['cached_header'][$username][$pageKey] = ob_get_clean();
echo $_SESSION['cached_header'][$username][$pageKey];
?>
