@extends('admin.index')
@section('cs')


@endsection
@section('bread')
    <li class="active" style="color: white;font-size: larger">Manage More Information</li>

    <li class="active" style="color: white;font-size: larger">Manage Tips</li>
@endsection
@section('content')
    <p>Total Trips:  <span style="color: #1d68a7">{{$trips_count}}</span></p>

    @foreach($trips as $trip)
        <div class="panel panel-primary">
            <div class="panel-heading">User Details
            </div>
            <div class="panel-body">
               <a href="#" class="fa fa-share-alt" style="float: right"> <span>{{\App\Models\Publishing::where('trip_id', $trip->id)->whereNotNull('sharer_id')->count()}}</span></a><br>
                <a href="#" class="fa fa-thumbs-up" style="float: right"> <span>{{\App\Models\Publishing::where('trip_id', $trip->id)->where('publisher_id',$trip->publisher->id)->first()->likes->count()}}</span></a>



                <div class="col-md-3">
                                    <img src="{{asset('/uploads/publishers/'.$trip->publisher->image)}}"
                    width="100px"
                    height="100px"
                    class="img-circle d-inline" alt="">
                </div>
                <div class="col-md-9">
                    <ul class="admin_info_new">
                        <div style="float: left">
                            <li>
                                <p><strong><i class="fa fa-user"></i> Name</strong>
                                                                <br>{{$trip->publisher->username}}</p>
                            </li>
                            <li>
                                <p><strong><i class="fa fa-envelope"></i> Email Address</strong>
                                    <br>{{$trip->publisher->email}}</p>
                            </li>
                            <li>
                                <p><strong><i class="fa fa-phone" aria-hidden="true"></i> Contact Us</strong>
                                    <br>{{$trip->publisher->mobile}}</p>
                            </li>

                            <li>
                                <p><strong><i class="fa fa-map-marker green" aria-hidden="true"></i> Start Address</strong>
                                    <br>{{$trip->start_address}}</p>
                            </li>

                            <li>
                                <p><strong><i class="fa fa-map-marker red" aria-hidden="true"></i> End Address</strong>
                                    <br>{{$trip->end_address}} </p>
                            </li>

                            <li>
                                <p><strong><i class="fa fa-clock-o" aria-hidden="true"></i>Started At</strong>
                                    <br>{{$trip->created_at}} </p>
                            </li>
                            <li>
                                <p><strong><i class="fa fa-clock-o" aria-hidden="true"></i>Ended At</strong>
                                    <br>{{$trip->ended_at}} </p>
                            </li>

                        </div>




                    </ul>


                </div>

                <a href="{{route('trip.delete',['id'=>$trip->id])}}" class="fa fa-trash" style="float: right"></a>

            </div>
        </div>
    @endforeach
    <div class="row col-md-12" style="text-align: center">
        {{$trips->links()}}
    </div>

@endsection
@section('js')


@endsection
