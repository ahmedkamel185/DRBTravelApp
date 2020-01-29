@extends('admin.index')
@section('cs')



@endsection
@section('bread')
    <li><a href="{{route('user.index')}}" style="color: white">Manage User</a></li>
    <li class="active" style="color: white;font-size: larger">User Details</li>
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
                                <br> {{$trip_completed}}</p>
                        </li>
                        <li>
                            <p><strong><i class="fa fa-exclamation-triangle"></i> Risk Reported</strong>
                                <br> {{$risk_reported}}</p>
                        </li>
                        <li>
                            <p><strong><i class="fa fa-map-marker" aria-hidden="true"></i> Suggested Places</strong>
                                <br> {{$suggest_places}}</p>
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
                        @foreach($trips as $trip)


                            <div class="panel panel-primary">
                                <div class="panel-heading"></div>
                                <div class="panel-body">


                                    <div class="col-md-12">
                                        <ul class="admin_info_new">
                                            <div style="float: left">
                                                <li class="fa fa-map-marker green" style="color: green"></li>&nbsp;
                                                <span
                                                    style="font-size: large">{{$trip->start_address}}</span><br>
                                                <li class="fa fa-map-marker red" style="color: red"></li>&nbsp; <span
                                                    style="font-size: large">{{$trip->end_address}}</span>
                                                <li>
                                                    <p><strong><i class="fa fa-time" aria-hidden="true"></i> Started At</strong>
                                                        <br>{{$trip->created_at->format('Y-m-d')}} </p>
                                                </li>
                                                <li>
                                                    <p><strong><i class="fa fa-time" aria-hidden="true"></i> Ended
                                                            At</strong>
                                                        <br>{{$trip->ended_at}} </p>
                                                </li>
                                            </div>

                                        </ul>


                                    </div>
                                    <div class="col-md-12" style="padding: 2px">
                                        @foreach($trip->resources as $resource)
                                            @if($resource->type == 'image')
                                                <img src="{{asset('/uploads/tripResources/'.$resource->resource)}}"
                                                     width="320"
                                                     height="240">
                                            @endif
                                        @endforeach

                                    </div>
                                    <div class="col-md-12">
                                        @foreach($trip->resources as $resource)
                                            @if($resource->type == 'vedio')
                                                <video width="320" height="240" controls>
                                                    <source
                                                        src="{{asset('/uploads/tripResources/'.$resource->resource)}}">
                                                </video>
                                            @endif
                                        @endforeach

                                    </div>


                                </div>
                            </div>



                        @endforeach
                    </div>
                    <div class="tab-pane" id="tab_2">
                        <div class="tab-content">
                            @foreach($suggest as $sug)
                                <div class="panel panel-primary">
                                    <div class="panel-heading"></div>
                                    <div class="panel-body">

                                        <div class="col-md-3">
                                            <img src="{{asset('/uploads/suggests/'.$sug->image)}}"
                                                 width="100px"
                                                 height="100px"
                                                 class="img-circle d-inline" alt="">
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="admin_info_new">
                                                <div style="float: left">
                                                    <li>
                                                        <p><strong><i class="fa fa-user"></i> Address</strong>
                                                            <br>{{$sug->address}}</p>
                                                    </li>
                                                    <li>
                                                        <p><strong><i class="fa fa-"></i>Description</strong>
                                                            <br>{{$sug->desc}}</p>
                                                    </li>

                                                </div>

                                            </ul>


                                        </div>


                                    </div>
                                </div>

                            @endforeach

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>







@endsection
@section('js')



@endsection
