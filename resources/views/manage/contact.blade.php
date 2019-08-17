@extends('admin.index')
@section('cs')

@endsection
@section('content')
    <div class="card">
        <form action="{{route('manage.contacts.update',['id'=>$terms->id])}}" method="post">
            @csrf
            <div class="form-group">
                <label>تواصل معنا</label>
                <input type="email" class="form-control" name="contact_us_ar" value="{{$terms->contact_us_ar}}">
            </div>

            <div class="form-group">
                <label>Contact us</label>
                <input type="email" class="form-control" name="contact_us_en" value="{{$terms->contact_us_en}}">
            </div>
            <button type="submit" class="btn btn-wide btn-lg btn-success">Submit</button>
        </form>
    </div>




@endsection
@section('js')

@endsection