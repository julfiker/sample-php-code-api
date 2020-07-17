<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Example 1</title>
    <link rel="stylesheet" href="css/pdf-style.css" media="all"/>
</head>
<body>
<header class="clearfix">
    <div id="logo">
        Photo at spoly
    </div>
    <br/>

    <h1>My personal information</h1>
    <div>
        <table style="width:100%">
            <tr>
                <td width="50%" style="border-right:1px solid #000">
                    <table width="100%" class="personal-info left">
                        <tr>
                            <td>#{{$user->id}} - {{$user->fullname}} </td>
                        </tr>
                        <tr>
                            <td>Email - {{$user->email}}</td>
                        </tr>
                        <tr>
                            <td>City - {{$user->current_city}}</td>
                        </tr>
                        <tr>
                            <td>Country - {{$user->current_country}} </td>
                        </tr>
                    </table>
                </td>
                <td width="50%" style="padding-left:10px;">
                    <table width="100%" class="personal-info right">
                        <tr>
                            <td>Gender - {{$user->gender}}</td>
                        </tr>
                        <tr>
                            <td>Date of birth - {{ \Carbon\Carbon::parse($user->birthday)->format('d.m.Y')}} </td>
                        </tr>
                        <tr>
                            <td>Birth of country - {{$user->birth_country}} </td>
                        </tr>
                        <tr>
                            <td>Nationality - @if ($user->Nationality) {{$user->Nationality->name}} @endif</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <br/>

    <h1>About me</h1>

    <div>{{$user->about_me}}</div>
</header>

<div>
    <div id="mysports">
        <div style="font-weight: bold">My sports</div>
        <table style="width:100%" border="0" cellpadding="0" border-spacing="0.5rem">
            <tr>
                <th style="width:50%">Name</th>
                <th>Created At</th>
            </tr>
            @foreach ($user->myOwnSports as $sport)
                <tr>
                    <td style="width:50%">
                        {{$sport->name}}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($sport->created_at)->format('d.m.Y H:i:s')}}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    <div id="myhotspots">
        <div style=" font-weight: bold">My hotspots</div>
        <table style="width:100%" border="0" cellpadding="0" border-spacing="0.5rem">
            <tr>
                <th style="width:50%">Name</th>
                <th>Created At</th>
            </tr>
            @foreach ($user->myOwnHotspots as $hotspot)
                <tr>
                    <td style="width:50%"2>
                        {{$hotspot->name}}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($hotspot->created_at)->format('d.m.Y H:i:s')}}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    <div id="myhotspots">
        <div style=" font-weight: bold">My Groups</div>
        <table style="width:100%" border="0" cellpadding="0" border-spacing="0.5rem">
            <tr>
                <th>Name</th>
                <th>Number of members</th>
                <th>Created At</th>
            </tr>
            @foreach ($user->myOwnGroups as $group)
                <tr>
                    <td>
                        {{$group->name}}
                    </td>
                    <td>{{$group->members()->count()}}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($group->created_at)->format('d.m.Y H:i:s')}}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

    <div id="myhotspots">
        <div style=" font-weight: bold">My Activities</div>
        <table style="width:100%" border="0" cellpadding="0" border-spacing="0.5rem">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Started At</th>
                <th>Ended At</th>
            </tr>
            @foreach ($user->myOwnActivities as $activity)
                <tr>
                    <td>
                        {{$activity->title}}
                    </td>
                    <td>{{ $activity->description}}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($activity->start_time)->format('d.m.Y H:i:s')}}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($activity->end_time)->format('d.m.Y H:i:s')}}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
</body>
</html>