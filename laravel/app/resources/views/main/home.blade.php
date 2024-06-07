@extends('layouts.default')

@section('page.title', config('app.name'))

@section('content')
    <div class="row">
        <div class="col-4">Name:</div>
        <div class="col-8">{{$name}}</div>
    </div>
    <div class="row">
        <div class="col-4">Age:</div>
        <div class="col-8">@if($age > 18) {{$age}} @else Вы слишком молоды! @endif</div>
    </div>
    <div class="row">
        <div class="col-4">Position:</div>
        <div class="col-8">{{$position}}</div>
    </div>
    <div class="row">
        <div class="col-4">Address:</div>
        <div class="col-8">{{$address}}</div>
    </div>
@endsection
