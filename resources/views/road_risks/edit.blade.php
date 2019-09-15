@extends('admin.index')
@section('cs')

@endsection
@section('bread')
    <li class="active" style="color: white;font-size: larger">Manage More Information</li>
    <li><a href="{{route('road.risks')}}" style="color: white">Manage Risks</a></li>
    <li class="active" style="color: white;font-size: larger">edit risk</li>
@endsection
@section('content')

    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit New Risk</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form role="form" action="{{route('risk.update',['id'=>$risk->id])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label for="risk">Enter Risk Name Arabic</label>
                            <input type="text" name="name_ar" class="form-control" id="risk"
                                   placeholder="الاسم باللغه العربيه" value="{{$risk->name_ar}}">
                        </div>
                        <div class="form-group">
                            <label for="risk_en">Enter Risk Name English</label>
                            <input type="text" name="name_en" class="form-control" id="risk_en"
                                   placeholder="Store Type English" value="{{$risk->name_en}}">
                        </div>
                        <div class="form-group">
                            <label for="icon">Upload Icon</label>
                            <input type="file" name="icon" class="form-control" id="icon">
                        </div>



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
