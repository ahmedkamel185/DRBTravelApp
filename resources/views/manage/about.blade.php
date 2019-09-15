@extends('admin.index')
@section('cs')

@endsection
@section('bread')
    <li><a href="{{route('manage.index')}}" style="color: white">Manage App Setting</a></li>
    <li class="active" style="color: white;font-size: larger"> About </li>
@endsection
@section('content')
    <form action="{{route('manage.about.update',['id'=>$terms->id])}}" method="post">
        @csrf
        <div class="form-group">
            <label>Textarea</label>
            <textarea class="form-control" name="about_ar" rows="15">{{$terms->about_ar}}</textarea>

        </div>
        <div class="form-group">
            <label>Textarea</label>
            <textarea class="form-control" name="about_en" rows="15">{{$terms->about_en}}</textarea>

        </div>

        <button type="submit" class="btn btn-wide btn-lg btn-success">Submit</button>
    </form>





@endsection
@section('js')

@endsection
