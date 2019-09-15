@extends('admin.index')
@section('cs')

@endsection
@section('bread')
    <li class="active" style="color: white;font-size: larger">Manage More Information</li>
    <li class="active" style="color: white;font-size: larger">Manage Images</li>
@endsection
@section('content')
    @foreach($images as $image)

        <div class="col-md-4" style="padding-bottom: 20px">
                <img src="{{asset('/uploads/tripResources/'.$image->resource)}}"
                     width="320"
                     height="240" style="padding-bottom: 10px">
            <br>
            <p>userName : {{$image->trip->publisher->username}}</p>

        </div>
    @endforeach

    <br>
    <div class="row col-md-12" style="text-align: center">
        {{$images->links()}}

    </div>





@endsection
@section('js')



@endsection
