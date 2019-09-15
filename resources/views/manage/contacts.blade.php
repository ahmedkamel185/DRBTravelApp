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
@endsection
@section('content')

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Manage Contacts</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>S.NO.</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Desc</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($messages as $message)
                    <tr>
                        <td>{{$message->id}}</td>
                        <td>{{$message->contact_number}}</td>
                        <td>{{$message->email}}</td>
                        <td>{{$message->subject}}</td>
                        <td>{{$message->desc}}</td>
                        @if($message->seen === 0)
                        <td>Not Seen</td>
                            @else
                            <td>Already seen</td>
                        @endif

                    </tr>
                @endforeach


                </tbody>

            </table>
        </div>
        <!-- /.box-body -->
    </div>



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
@endsection
