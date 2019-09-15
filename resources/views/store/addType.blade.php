@extends('admin.index')
@section('cs')

@endsection
@section('bread')
    <li><a href="{{route('store.index')}}" style="color: white">Manage Service Providers</a></li>
    <li class="active" style="color: white;font-size: larger"> Add New Service Type</li>
@endsection
@section('content')

    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Add New Service Type</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form role="form" action="{{route('store.store.type')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label for="store">Enter Store Type Arabic</label>
                            <input type="text" name="name_ar" class="form-control" id="store"
                                   placeholder="الاسم باللغه العربيه" value="">
                        </div>
                        <div class="form-group">
                            <label for="store">Enter Store Type English</label>
                            <input type="text" name="name_en" class="form-control" id="store"
                                   placeholder="Store Type English" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" name="image" class="form-control" id="image"
                               value="">
                    </div>


                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('js')




@endsection
