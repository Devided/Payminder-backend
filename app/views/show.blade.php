<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel PHP Framework</title>
    <style>
        @import url(//fonts.googleapis.com/css?family=Lato:700);

        body {
            margin:20px;
            font-family:'Lato', sans-serif;
            text-align:left;
            color: #000;
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
    <hr>
</p>
<p>
    @foreach($friends as $friend)
    <li><span style="text-align: left">{{ $friend->first_name }} {{ $friend->last_name }}</span><span style="text-align:right">bedrag €{{ $friend->amount }}</span></li>
    @endforeach
</p>
</body>
</html>
