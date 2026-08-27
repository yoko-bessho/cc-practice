<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', '日報管理')</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body>
        <div class="container">
            @if (session('status'))
                <div class="flash">{{ session('status') }}</div>
            @endif

            @yield('content')
        </div>
    </body>
</html>
