@extends('admin.index')
@section('cs')

@endsection
@section('content')

    @if(session()->has('success'))
        <h2 class="page-header">Manage In-App Content</h2>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-check"></i></h4>
            {{session()->get('success')}}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab"><h4>Terms and Conditions</h4></a></li>
                    <li><a href="#tab_2" data-toggle="tab"><h4>Contact Us</h4></a></li>
                    <li><a href="#tab_3" data-toggle="tab"><h4>About Us</h4></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                            <h2 style="display: inline !important"> Terms and Conditions </h2>
                            <p class="display-block-xs" style="float: right"><a
                                        href="{{route('manage.terms.edit',['id'=>$manages->id])}}"
                                        class="fa fa-edit "></a>
                            </p>
                        <p>
                            {{$manages->terms_ar}}
                        </p>
                        <p>
                            {{$manages->terms_en}}
                        </p>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2">
                        <h2>Contact Us</h2>
                        <p class="display-block-xs" style="float: right"><a
                                    href="{{route('manage.contacts.edit',['id'=>$manages->id])}}"
                                    class="fa fa-edit "></a>
                        </p>

                        <p>{{$manages->contact_us_ar}}</p>
                        <p>{{$manages->contact_us_en}}</p>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_3">
                        <h2>About Us</h2>
                        <p class="display-block-xs" style="float: right"><a
                                    href="{{route('manage.about.edit',['id'=>$manages->id])}}"
                                    class="fa fa-edit "></a>
                        </p>
                        <p>{{$manages->about_ar}}</p>
                        <p>{{$manages->about_en}}</p>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- nav-tabs-custom -->
        </div>
        <!-- /.col -->

        <!-- /.col -->
    </div>

@endsection
@section('js')

@endsection