@extends('admin.index')
@section('cs')


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css"/>

    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <style>
        /* The switch - the box around the slider */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        /* Hide default HTML checkbox */
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* The slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>



@endsection
@section('bread')
    <li><a href="{{route('store.index')}}" style="color: white">Manage Service Providers</a></li>
    <li class="active" style="color: white;font-size: larger">Store Provider Details</li>
@endsection

@section('content')
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
                            <p>
                                @if($place->status === 0)
                                    <span >blocked</span>
                                @else
                                    <span >active</span>
                                @endif
                                <label class="switch">
                                    <input data-id="{{$place->id}}"
                                           class="toggle" type="checkbox"

                                           data-status="{{$place->status}}"
                                           @if( $place->status == 0) checked @endif
                                    >
                                    <span class="slider round"></span>
                                </label>
                            </p>
                        </div>
                    </div>
                @endforeach

            </div>


        </div>
    </div>






@endsection
@section('js')

    <script>
        $(function () {
            console.log('loaded');

            $('.panel-body').on('change', ".toggle", function () {

                var status = $(this).data('status') == 1 ? 0 : 1;
                $(this).data('status', status)
                console.log(status);
                var user_id = $(this).data('id');
                console.log(user_id);


                $.ajax({

                    type: "GET",

                    dataType: "json",

                    url: '/store/address-change',

                    data: {'status': status, 'user_id': user_id},

                    success: function (data) {

                        console.log(data.success)

                    }

                });

            })

        });

    </script>

@endsection
