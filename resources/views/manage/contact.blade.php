@extends('admin.index')
@section('cs')

@endsection
@section('bread')
    <li><a href="{{route('manage.index')}}" style="color: white">Manage App Setting</a></li>
    <li class="active" style="color: white;font-size: larger"> Contacts </li>
@endsection
@section('content')
    <div class="card">
        <form action="{{route('manage.contacts.update',['id'=>$terms->id])}}" method="post">
            @csrf
            <div class="form-group">
                <label>تواصل معنا</label>
                <input type="text" class="form-control" name="mobile" value="{{$terms->mobile}}">
            </div>

{{--            <div class="form-group">--}}
{{--                <label>Contact us</label>--}}
{{--                <input type="email" class="form-control" name="contact_us_en" value="{{$terms->contact_us_en}}">--}}
{{--            </div>--}}
            <button type="submit" class="btn btn-wide btn-lg btn-success">Submit</button>
        </form>
    </div>




@endsection
@section('js')

@endsection
