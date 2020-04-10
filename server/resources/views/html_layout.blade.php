<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">
    <title>@yield(('title'))</title>
    <link rel="stylesheet" type="text/css" href="/styles/main.css">
</head>
<body class="model-{{$entity->model ?? 'undefined'}} view-{{$entity->view ?? 'undefined'}} short-id-{{$entity->short_id ?? 'undefined'}}">
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
