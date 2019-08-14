@extends('admin.index')
@section('cs')

@endsection
@section('content')
    <form action="{{route('manage.about.update',['id'=>$terms->id])}}" method="post">
        @csrf
        <div class="form-group">
            <label>Textarea</label>
            <textarea class="form-control" name="about_us" rows="15">{{$terms->about_us}}</textarea>

        </div>
        <button type="submit" class="btn btn-wide btn-lg btn-success">Submit</button>
    </form>





@endsection
@section('js')

@endsection