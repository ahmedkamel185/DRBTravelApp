@extends('admin.index')
@section('cs')
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{'/design/adminlte'}}/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{'/design/adminlte'}}/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{'/design/adminlte'}}/bower_components/Ionicons/css/ionicons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet"
          href="{{'/design/adminlte'}}/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{'/design/adminlte'}}/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{'/design/adminlte'}}/dist/css/skins/_all-skins.min.css">

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
    <li class="active" style="color: white;font-size: larger">Manage More Information</li>
    <li><a href="{{route('road.risks')}}" style="color: white">Manage Risks</a></li>
    <li class="active" style="color: white;font-size: larger">list of risks</li>

@endsection
@section('content')
    <div>
        <div class="box-header">
            <h1 style="color: blue" class="box-title">Manage Risks</h1>
            <p>Total no of Risks type: <span style="color: #1d68a7">{{$risks_type_count}}</span></p>
            <a href="{{route('road.risks.add')}}" class="fa fa-plus-circle" style="float: right">Add New Risk</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>S.NO.</th>
                    <th>Road Risk Image</th>
                    <th>Road Risk Name</th>
                    <th>edit</th>
                    <th>delete</th>

                </tr>
                </thead>
                <tbody>
                @foreach($risk_type as $risk)
                    <tr>

                        <td>{{$risk->id}}</td>
                        <td><img src="{{asset('uploads/riskTypes/'.$risk->icon)}}"
                                 width="100px"
                                 height="100px"
                                 class="img-circle d-inline" alt="">
                        </td>
                        <td>{{$risk->name_en}}</td>
                        <td><a class="fa fa-edit" href="{{route('risk.edit',['id'=>$risk->id])}}"></a></td>
                        <td><a class="fa fa-trash" href="{{route('risk.delete',['id'=>$risk->id])}}"></a></td>
                    </tr>
                @endforeach

                </tbody>

            </table>
        </div>
    </div>
    <!-- /.box-body -->



@endsection
@section('js')
    <script>

        $(function () {
            $('#example1').DataTable()
            $('#example2').DataTable({
                'paging': true,
                'lengthChange': false,
                'searching': false,
                'ordering': true,
                'info': true,
                'autoWidth': false
            })
        })


    </script>


@endsection
