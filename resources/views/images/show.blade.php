@extends('admin.index')
@section('cs')
@endsection
@section('bread')
    <li class="active" style="color: white;font-size: larger">Manage More Information</li>
    <li><a href="{{route('image.index')}}" style="color: white">Manage Images</a></li>
    <li class="active" style="color: white;font-size: larger">Show</li>
@endsection
@section('content')
                    <img src="{{asset('/uploads/tripResources/'.$image->resource)}}"
                         width="1000"
                         height="500" style="padding-left: 80px;margin: auto;  ">

@endsection
@section('js')



@endsection
