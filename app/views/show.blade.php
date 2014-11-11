<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel PHP Framework</title>
    <style>
        @import url(//fonts.googleapis.com/css?family=Lato:700);

        body {
            margin-top:20px;
            font-family:'Lato', sans-serif;
            text-align:left;
            color: #000;
            margin-left:0px;
            margin-right:0px;
            padding-left:0px;
            padding-right: 0px;
        }

        .welcome {
            width: 300px;
            height: 200px;
            position: absolute;
            left: 50%;
            top: 50%;
            margin-left: -150px;
            margin-top: -100px;
        }

        a, a:visited {
            text-decoration:none;
        }

        h1 {
            font-size: 32px;
            margin: 16px 0 0 0;
        }
    </style>
</head>
<body>
<p style="text-align: center">
    Datum: {{ date('d/m/y', $payminder->end_time); }}
</p>
<p>
    <hr style="margin-left:20px; margin-right:20px;">
</p>
<p>
    @foreach($friends as $friend)
    @if($friend->paid)
    <div style="width:100%; height:60px; background:#bcd42a; border-bottom-width: 1px; border-bottom-color: #000000; vertical-align: middle; margin-top:1px; padding-top:30px;">
        <span style="text-align: left; margin-left: 20px; margin-top:20px;">{{ $friend->first_name }} {{ $friend->last_name }}</span><span style="position:absolute; text-align:right; right:0px; margin-right:20px;">bedrag €{{ $friend->amount }}</span>
    </div>
    @else
    <div style="width:100%; height:60px; background:#ff5c4f; border-bottom-width: 1px; border-bottom-color: #000000; vertical-align: middle; margin-top:1px; padding-top:30px;">
        <span style="text-align: left; margin-left: 20px; margin-top:20px;">{{ $friend->first_name }} {{ $friend->last_name }}</span><span style="position:absolute; text-align:right; right:0px; margin-right:20px;">bedrag €{{ $friend->amount }}</span>
    </div>
    @endif
    @endforeach
</p>
</body>
</html>
