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
@section('bread')
    <li class="active" style="color: white;font-size: larger">Manage Notifications</li>
@endsection
@section('content')

        <div class="box-header">
            <h3 class="box-title">Manage Notifications</h3>
            <span style="float: right" class="fa fa-add"><a href="{{route('notification.add')}}">Add Notification</a></span>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>S.NO.</th>
                    <th>Title</th>
                    <th>Notification Description</th>
                    <th>Edit</th>
                    <th>Send</th>
                </tr>
                </thead>
                <tbody>
                @foreach($notifications as $notification)
                    <tr>
                        <td>{{$notification->id}}</td>
                        <td>{{$notification->title}}</td>
                        <td>{{$notification->desc}}</td>
                        <td><a  class="fa fa-edit" href="{{route('notification.edit',['id'=>$notification->id])}}"></a></td>
                        <td><a  class="fa fa-paper-plane" href="{{route('notification.send',['id'=>$notification->id])}}"></a></td>

                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
        <!-- /.box-body -->




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
