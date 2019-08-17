@extends('admin.index')
@section('cs')

@endsection
@section('content')
    @foreach($videos as $video)

        <video width="320" height="240" controls>
            <source src="{{asset('/uploads/tripResources/'.$video->resource)}}">
        </video>



    @endforeach
    <br>
    <div class="row col-md-12" style="text-align: center">
        {{$videos->links()}}

    </div>





@endsection
@section('js')



@endsection