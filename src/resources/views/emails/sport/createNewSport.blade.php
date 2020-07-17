@extends('layouts.email')

@section('body')
    <div style="text-align: center">
        Hi {{$admin}},<br>
        <br>
        <br>
        User has created new sport<br>
        <br>
        {{ $event->sport->name }}
    </div>
@endsection