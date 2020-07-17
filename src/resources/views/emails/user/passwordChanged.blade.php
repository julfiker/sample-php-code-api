@extends('layouts.email')

@section('body')
    <div style="text-align: center">
        Hi {{$first_name}},<br>
        <br>
        <br>
        Your password has been changed successfully.<br>
        <br>
        You're all set to enjoy your favourite sport again!<br>
        <br>
        <br>
        High five!<br>
        <br>
        <br>
        Your buddies from <b>Spoly</b><br>
        <br>
        W: <a href="https://www.spoly.com">spoly.com</a><br>
        E: <a href="mailto:feedback@spoly.com">feedback@spoly.com</a><br>
        F: <a href="https://www.facebook.com/spolyapp">facebook.com/spolyapp</a><br>
        I: <a href="https://www.instagram.com/spoly_app">@spoly_app</a><br>
    </div>
@endsection