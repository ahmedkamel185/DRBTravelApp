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






@endsection
@section('js')



@endsection
