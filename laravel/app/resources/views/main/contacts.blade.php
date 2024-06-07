@extends('layouts.default')

@section('page.title', config('app.name'))

@section('content')
    <div class="row">
        <div class="col-4">Address:</div>
        <div class="col-8">{{$address}}</div>
    </div>
    <div class="row">
        <div class="col-4">Post code:</div>
        <div class="col-8">{{$post_code}}</div>
    </div>
    <div class="row">
        <div class="col-4">Email:</div>
        <div class="col-8">@if($email) {{$email}} @else Адрес электронной почты не указан @endif</div>
    </div>
    <div class="row">
        <div class="col-4">Phone:</div>
        <div class="col-8">{{$phone}}</div>
    </div>
@endsection
