@extends('admin.index')
@section('cs')



@endsection
@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Manage Service Providers</h3>
            <a href="{{route('store.address',['id'=>$store->id])}}" class="fa fa-plus-circle" style="float: right">Add Address</a>
        </div>
        <hr>




    <div class="panel panel-primary">

        <div class="panel-heading">Store Provider Details</div>
        <div class="panel-body">

            <div class="col-md-12">

                <div class="col-md-6">

                    <p><strong><i class="fa fa-user"></i> Service Provider Name</strong>
                        <br>{{$store->store_name}}</p>
                </div>
                <div class="col-md-6">

                    <p><strong><i class="fa fa-envelope"></i> Service Type</strong>
                        <br>{{$store->StoreType->name_en}}</p>
                </div>
                @foreach($store->storePlaces as $place)
                    <div class="col-md-6">
                        <div class="col-md-3">
                            <p><strong><i class="fa fa-map-marker"></i> Address</strong>
                                <br>{{$place->address}}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="display-block-xs" style="float: right; padding-left: 40px"><a
                                        href="{{route('store.edit',['id'=>$place->id])}}"
                                        class="fa fa-edit "></a>
                            </p>
                            <p class="display-block-xs" style="float: right"><a
                                        href="{{route('store.delete',['id'=>$place->id])}}"
                                        class="fa fa-trash "></a>
                            </p>
                        </div>
                    </div>
                @endforeach

            </div>


        </div>
    </div>



</div>


@endsection
@section('js')



@endsection
