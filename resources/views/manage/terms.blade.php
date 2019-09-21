@extends('admin.index')
@section('cs')

@endsection
@section('bread')
    <li><a href="{{route('manage.index')}}" style="color: white">Manage App Setting</a></li>
    <li class="active" style="color: white;font-size: larger"> Terms </li>
@endsection
@section('content')
    <form action="{{route('manage.terms.update',['id'=>$terms->id])}}" method="post">
        @csrf
        <div class="form-group">
            <label>الشروط والاحكام</label>
            <textarea class="form-control" name="terms_ar" rows="5">{{$terms->terms_ar}}</textarea>

        </div>

        <div class="form-group">
            <label>Terms and Condition</label>
            <textarea class="form-control" name="terms_en" rows="5">{{$terms->terms_en}}</textarea>

        </div>
        <button type="submit" class="btn btn-wide btn-lg btn-success">Submit</button>
    </form>





@endsection
@section('js')

@endsection
