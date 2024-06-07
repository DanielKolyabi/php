@extends('layouts.default')

@section('page.title', config('app.name'))

@section('content')
    @if(isset($body))
        @if(!empty($title)) <h1>{{$title}}</h1> @endif
        @if($data !== null) {{view($body, (array)$data)}} @else {{view($body)}} @endif
    @else
        {{view('layouts.main')}}
    @endif
@endsection
