@extends('admin.index')
@section('cs')


@endsection
@section('content')

    @foreach($trips as $trip)
        <div class="panel panel-primary">
            <div class="panel-heading">User Details</div>
            <div class="panel-body">
                <a href="{{route('trip.delete',['id'=>$trip->id])}}" class="fa fa-trash" style="float: right"></a>

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
                        </div>




                    </ul>


                </div>


            </div>
        </div>
    @endforeach
    <div class="row col-md-12" style="text-align: center">
        {{$trips->links()}}
    </div>

@endsection
@section('js')


@endsection
