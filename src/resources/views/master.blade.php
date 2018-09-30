@php $csrf_token = csrf_token() @endphp<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ $csrf_token }}">
    <script>
        window.Laravel = { csrfToken: '{{$csrf_token}}' }
    </script>

    <link rel="icon" type="image/png" href="{{ url('jianastrero/jugger-api/favicon.png') }}">

    <title>Jugger API</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Oswald:700|Nunito:200,600" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

    <!-- Styles -->
    <link href="{{ url('css/jugger-api.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('jianastrero/jugger-api/css/jugger-api-custom.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ url('jianastrero/jugger-api/css/animate.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
<div id="app">
    @yield('body')
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="{{ url('js/jugger-api.js') }}"></script>
</body>
</html>
