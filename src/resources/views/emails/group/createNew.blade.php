@extends('layouts.email')

@section('body')
    <div style="text-align: center">
        Hi {{$admin}},<br>
        <br>
        <br>
        User has created new group<br>
        <br>
        {{ $event->group->name }}
    </div>
@endsection