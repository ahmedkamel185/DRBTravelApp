@extends('admin.index')
@section('cs')

@endsection
@section('content')
    <form action="{{route('manage.terms.update',['id'=>$terms->id])}}" method="post">
        @csrf
        <div class="form-group">
            <label>Textarea</label>
            <textarea class="form-control" name="terms" rows="15">{{$terms->terms}}</textarea>

        </div>
        <button type="submit" class="btn btn-wide btn-lg btn-success">Submit</button>
    </form>





@endsection
@section('js')

@endsection