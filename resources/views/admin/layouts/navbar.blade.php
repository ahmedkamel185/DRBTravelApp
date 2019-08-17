<header class="main-header">
    <!-- Logo -->
    <a href="#" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>A</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>Admin</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        @include('admin.layouts.menu')
    </nav>
</header>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{url('/')}}/design/adminlte/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>Admin</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <li class="active treeview">
                <a href="{{route('home')}}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                    <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
                </a>
            </li>

            <li><a href="{{route('home')}}"><i class="fa fa-tasks "></i> <span>Home</span></a></li>

            <li><a href="{{route('user.index')}}"><i class="fa fa-user-circle "></i> <span>Manage Users</span></a></li>


            <li><a href="{{route('store.index')}}"><i class="fa fa-tasks  "></i> <span>Manage Service Providers </span></a></li>

            <li><a href="{{route('place.index')}}"><i class="fa fa-tasks "></i> <span>Manage Suggested places </span></a></li>

            <li class="treeview">
                <a href="#">
                    <i class="fa fa-tasks"></i>
                    <span>Manage More Informations</span>
                    <span class="pull-right-container">
            </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{route('road.index')}}"><i class="fa fa-circle-o"></i> Road Risk</a></li>
                    <li><a href="{{route('trip.index')}}"><i class="fa fa-circle-o"></i> Trips</a></li>
                    <li><a href="{{route('image.index')}}"><i class="fa fa-circle-o"></i> Image Gallery</a></li>
                    <li><a href="{{route('video.index')}}"><i class="fa fa-circle-o"></i> Video Gallery</a></li>
                </ul>
            </li>

            <li><a href="{{route('feedback.index')}}"><i class="fa fa-tasks "></i> <span>Manage FeedBack </span></a></li>

            <li><a href="{{route('notification.index')}}"><i class="fa fa-tasks "></i> <span>Manage Notifications </span></a></li>

            <li><a href="{{route('manage.index')}}"><i class="fa fa-tasks "></i> <span>Manage In-App Content </span></a></li>





        </ul>
    </section>
    <!-- /.sidebar -->
</aside>