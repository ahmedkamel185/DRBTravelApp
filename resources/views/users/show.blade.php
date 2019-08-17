@extends('admin.index')
@section('cs')



@endsection
@section('content')



    <div class="panel panel-primary">
        <div class="panel-heading">User Details</div>
        <div class="panel-body">

            <div class="col-md-3">
                <img src="{{asset('/uploads/publishers/'.$user->image)}}"
                     width="100px"
                     height="100px"
                     class="img-circle d-inline" alt="">
            </div>
            <div class="col-md-6">
                <ul class="admin_info_new">
                    <div style="float: left">
                        <li>
                            <p><strong><i class="fa fa-user"></i> Name</strong>
                                <br>{{$user->username}}</p>
                        </li>
                        <li>
                            <p><strong><i class="fa fa-envelope"></i> Email Address</strong>
                                <br>{{$user->email}}</p>
                        </li>
                        <li>
                            <p><strong><i class="fa fa-phone" aria-hidden="true"></i> Contact Us</strong>
                                <br> {{$user->mobile}}</p>
                        </li>
                    </div>
                    <div style="float: right">
                        <li>
                            <p><strong><i class="fa fa-road"></i> Trips Completed</strong>
                                <br> 0</p>
                        </li>
                        <li>
                            <p><strong><i class="fa fa-exclamation-triangle"></i> Risk Reported</strong>
                                <br> 0</p>
                        </li>
                        <li>
                            <p><strong><i class="fa fa-map-marker" aria-hidden="true"></i> Suggested Places</strong>
                                <br> 0</p>
                        </li>
                    </div>
                </ul>


            </div>


        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab"><h4>Trips</h4></a></li>
                    <li><a href="#tab_2" data-toggle="tab"><h4>Suggested Places </h4></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <h2 style="display: inline !important"> Trips </h2>
                        @foreach($trips as $trip)
                            <div>
                                <div>
                                    <h4>Started Location : {{$trip->start_address}} </h4>
                                    <h4>Ended Location : {{$trip->end_address}} </h4>
                                    <h4>Distance : {{$trip->distance}} </h4>

                                </div>


                            </div>
                            <br>
                            <div>
                                @foreach($trip->resources as $resource)
                                    @if($resource->type ==='image')
                                        <img src="{{asset('/uploads/tripResources/'.$resource->resource)}}"
                                             width="320"
                                             height="240">
                                    @else

                                        <video width="320" height="240" controls>
                                            <source src="{{asset('/uploads/tripResources/'.$resource->resource)}}">
                                        </video>

                                    @endif

                                @endforeach
                            </div>
                            <hr>
                            <hr>
                            <hr>


                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    <!-- /.tab-content -->
    <!-- nav-tabs-custom -->
    </div>
    <!-- /.col -->

    <!-- /.col -->
    </div>







@endsection
@section('js')



@endsection
