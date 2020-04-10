<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">
    <title>@yield(('title'))</title>
    <link rel="stylesheet" type="text/css" href="/styles/main.css">
</head>
<body>
<header>

</header>
<main>
    @yield('main')
</main>
<footer>

</footer>
@include('html.partials.debug')
</body>
</html>
