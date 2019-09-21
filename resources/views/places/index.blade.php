@extends('admin.index')
@section('cs')

@endsection
@section('bread')
    <li class="active" style="color: white;font-size: larger">Manage Suggested Places</li>
@endsection
@section('content')

    <div class="box-header">
        <a href="{{route('place.add')}}" class="fa fa-plus-circle" style="float: right">Add Place</a>

    </div>
    @foreach($places as $place)
        <div class="panel panel-primary">
            <div class="panel-heading">

            </div>

            <div class="panel-body">
                <a href="{{route('place.edit',['id'=>$place->id])}}" class="fa fa-edit" style="float: right"></a><br>
                <a href="{{route('place.delete',['id'=>$place->id])}}" class="fa fa-trash" style="float: right"></a>


                <div class="col-md-3">
                    <img src="{{asset('/uploads/suggests/'.$place->image)}}"
                         width="200px"
                         height="200px"
                         class="" alt="">
                </div>
                <div class="col-md-6">
                    <ul class="admin_info_new">
                        <div style="float: left">
                            <li>
                                <p><strong><i class="fa text"></i> Description</strong>
                                    <br>{{$place->desc}}</p>
                            </li>
                            <li>
                                <p><strong><i class="fa fa-user"></i> Address</strong>
                                    <br>{{$place->address}}</p>
                            </li>

                        </div>
                    </ul>


                </div>


            </div>
        </div>
    @endforeach

    <div style="text-align: center">
        {{$places->links()}}
    </div>




@endsection
@section('js')

@endsection





