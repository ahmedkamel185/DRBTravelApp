@include('admin.layouts.header')

@include('admin.layouts.navbar')

<!-- Content Wrapper. Contains page content بيسسيبيس -->
<div class="content-wrapper" style="background-color: white">
    <!-- Content Header (Page header) -->
    <section class="content-header">
{{--        <h1>--}}
{{--            Dashboard--}}
{{--            <small>Control panel</small>--}}
{{--        </h1>--}}
{{--        <ol class="breadcrumb">--}}
{{--            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>--}}
{{--            <li class="active">Dashboard</li>--}}
{{--        </ol>--}}
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="loading">Loading&#8230;</div>
    <div class="loading">
        <div class="sk-folding-cube">
            <div class="sk-cube1 sk-cube"></div>
            <div class="sk-cube2 sk-cube"></div>
            <div class="sk-cube4 sk-cube"></div>
            <div class="sk-cube3 sk-cube"></div>
        </div>
    </div>


        @include('admin.layouts.message')
        <ul class="breadcrumb" style="background-color: #1d68a7">
            <li><a href="{{route('home')}}" style="color: white">Home</a></li>
            @yield('bread')
        </ul>
        @yield('content')




    </section>
    <!-- /.content -->
</div>
@include('admin.layouts.footer')
