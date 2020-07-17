@extends('layouts.email')

@section('body')
    <b>Feedback from {{$fullName}} ({{$email}})</b><br/>
    <hr/>
    {{$content}}
@endsection


