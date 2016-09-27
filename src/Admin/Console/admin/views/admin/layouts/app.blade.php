<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>@yield('title')</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="{{ asset('admin/plugins/bootstrap/css/bootstrap.min.css') }}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('admin/css/AdminLTE.min.css') }}">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
            folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="{{ asset('admin/css/skins/_all-skins.min.css') }}">
        <!-- iCheck -->
        <link rel="stylesheet" href="{{ asset('admin/plugins/iCheck/flat/blue.css') }}">
        @yield('admin-css')
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            .error-messages {
                list-style: none;
            }
            .overflow-hidden {
                overflow: hidden;
            }
        </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
        <?php $admin = auth()->guard('admin')->user(); ?>
        <div class="wrapper">
            <header class="main-header">
                <!-- Logo -->
                <a href="{{ route('admin') }}" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><b>A</b>LT</span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><b>Admin</b>LTE</span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    </a>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="{{ $admin->image or asset('admin/img/user2-160x160.jpg') }}" class="user-image" alt="User Image">
                                <span class="hidden-xs">{{ $admin->name }}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="{{ $admin->image or asset('admin/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
                                        <p>
                                            {{ $admin->name }}
                                            <small>Member since {{ $admin->created_at->format('M Y') }}</small>
                                        </p>
                                    </li>
                                    
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="{{ route('admin.profile') }}" class="btn btn-default btn-flat">Profile</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="{{ route('admin.logout') }}" class="btn btn-default btn-flat">Sign out</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="{{ $admin->image or asset('admin/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
                        </div>
                        <div class="pull-left info">
                            <p>{{ $admin->name }}</p>
                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>
                    
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    {!! $AdminNavBar->asUl(['class' => 'sidebar-menu']) !!}
                </section>
                <!-- /.sidebar -->
            </aside>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        @yield('title')
                    </h1>
                    @yield('breadcrumb')
                </section>
                <!-- Main content -->
                <section class="content">
                    @include('flash::message')
                    @yield('content')
                </section>
                <!-- /.content -->
            </div>
        </div>
        <!-- ./wrapper -->
        <!-- jQuery 2.2.3 -->
        <script src="{{ asset('admin/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="{{ asset('admin/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
        <!-- AdminLTE App -->
        <script src="{{ asset('admin/js/app.min.js') }}"></script>
        @yield('admin-js')
        <script>
            jQuery(document).ready(function(){
                var childrens = $('.main-sidebar .sidebar-menu ul');

                for(var i = 0; i < childrens.length; i++)
                {
                    if (!$(childrens[i]).hasClass('treeview-menu'))
                    {
                        $(childrens[i]).addClass('treeview-menu');
                    }
                }

                if ($('.data-table-advance').length) {
                    $('.data-table-advance').DataTable({
                        "paging":       false,
                        "lengthChange": true,
                        "searching":    true,
                        "ordering":     true,
                        "info":         true,
                        "autoWidth":    false
                    });
                }

                if ($('.datepicker').length)
                {
                    $('.datepicker').datepicker({
                        autoclose: true,
                        format : 'dd-mm-yyyy'
                    });
                }
            });
        </script>
    </body>
</html>