@extends('admin.index')
@section('cs')

@endsection
@section('content')
    @foreach($images as $image)

        <div class="col-md-4" style="padding-bottom: 20px">
                <img src="{{asset('/uploads/tripResources/'.$image->resource)}}"
                     width="320"
                     height="240">

        </div>
    @endforeach
    <br>
    <div class="row col-md-12" style="text-align: center">
        {{$images->links()}}

    </div>





@endsection
@section('js')



@endsection