@extends('admin.index')
@section('cs')

@endsection
@section('bread')
    <li class="active" style="color: white;font-size: larger">Manage App Setting</li>
@endsection
@section('content')


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
                    @if(App\Models\Setting::first())

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

                            <p>{{$manages->mobile}}</p>
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
                @endif
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
