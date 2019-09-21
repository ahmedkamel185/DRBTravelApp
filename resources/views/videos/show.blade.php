@extends('admin.index')
@section('cs')

@endsection
@section('bread')
    <li class="active" style="color: white;font-size: larger">Manage More Information</li>
    <li><a href="{{route('video.index')}}" style="color: white">Manage video</a></li>

    <li class="active" style="color: white;font-size: larger">Manage Videos</li>
@endsection
@section('content')
        <div class="col-md-4" style="padding-bottom: 20px; padding-left: 100px">

            <video width="820" height="540" controls>
                <source src="{{asset('/uploads/tripResources/'.$video->resource)}}" autostart="false">
            </video>
        </div>




@endsection
@section('js')



@endsection
