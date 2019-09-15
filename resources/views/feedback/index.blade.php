@extends('admin.index')
@section('cs')
@endsection
@section('bread')
    <li class="active" style="color: white;font-size: larger">Manage FeedBack</li>
@endsection
@section('content')
    @foreach($feedbacks as $feedback)
        <div class="panel panel-primary">
            <div class="panel-heading">{{$feedback->publisher->username}}</div>
            <div class="panel-body">

                <div class="col-md-3">
                    <img src="{{asset('uploads/publishers/'.$feedback->publisher->image)}}"
                         width="100px"
                         height="100px"
                         class="img-circle d-inline" alt="">
                </div>
                <div class="col-md-6">
                    <ul class="admin_info_new">
                        <div style="float: left">
                            <li>
                                <p><strong><i class="fa fa-user"></i> Number</strong>
                                    <br>{{$feedback->contact_number}}</p>
                            </li>
                            <li>
                                <p><strong><i class="fa fa-envelope"></i> Email Address</strong>
                                    <br>{{$feedback->email}}</p>
                            </li>
                            <li>
                                <p><strong><i class="fa fa-envelope"></i> Email Address</strong>
                                    <br>{{$feedback->created_at->diffForHumans()}}</p>
                            </li>

                            <li>
                                <p><strong><i class="fa fa-text-width" aria-hidden="true"></i> Subject</strong>
                                    <br> {{$feedback->subject}}</p>
                            </li>
                        </div>
                    </ul>


                </div>


            </div>
        </div>
    @endforeach

    <div style="text-align: center">
        {{$feedbacks->links()}}
    </div>




@endsection
@section('js')

@endsection
