@extends('admin.index')
@section('cs')

@endsection

@section('content')
@section('bread')
    <li><a href="{{route('notification.index')}}" style="color: white">MManage Notifications</a></li>
    <li class="active" style="color: white;font-size: larger"> Add New Notification </li>
@endsection

    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Add Notification</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form role="form" action="{{route('notification.store')}}" method="post">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label for="store">Enter Title</label>
                            <input type="text" name="title" class="form-control" id="store"
                                   placeholder="title of notification" value="{{old('title')}}">
                        </div>
                        <div class="form-group">
                            <label for="store">Enter Description</label>
                            <textarea name="desc" id="" cols="30" rows="10"
                                      class="form-control">{{old('desc')}}</textarea>

                        </div>


                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('js')




@endsection
