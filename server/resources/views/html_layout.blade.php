<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="UTF-8">
    <title>@yield(('title'))</title>
    <link rel="stylesheet" type="text/css" href="/styles/main.css">
</head>
<body class="{{isset($entity) ? 'model-'.$entity->model.' view-'.$entity->view.' id-'.$entity->id : ''}}">
<header>

</header>
<main>
    @yield('main')
</main>
<footer>
{{ trans("texts.language") }}: {{ trans("texts.$lang") }}
</footer>
@include('html.partials.debug')
</body>
</html>
