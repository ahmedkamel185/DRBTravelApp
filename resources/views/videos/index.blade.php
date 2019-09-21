@extends('admin.index')
@section('cs')

@endsection
@section('bread')
    <li class="active" style="color: white;font-size: larger">Manage More Information</li>
    <li class="active" style="color: white;font-size: larger">Manage Videos</li>
@endsection
@section('content')
    @foreach($videos as $video)
        <a href="{{route('video.video',['id'=>$video->id])}}"><div class="col-md-4" id="img-zoom" style="padding-bottom: 20px">

        <video width="320" height="240" controls>
            <source src="{{asset('/uploads/tripResources/'.$video->resource)}}" autostart="false">
        </video>
            <p>userName : {{$video->trip->publisher->username}}</p>

            </div></a>



    @endforeach
    <br>
    <div class="row col-md-12" style="text-align: center">
        {{$videos->links()}}

    </div>





@endsection
@section('js')



@endsection
