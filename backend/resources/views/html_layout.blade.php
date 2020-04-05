<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@yield(('title'))</title>
</head>
<body>
<header>

</header>
<main>
    @yield('main')
</main>
<footer>
    @isset($lang)
        <p>Lang: {{ $lang }}</p>
    @endisset

    @isset($entity)
    Entity:
    <pre>
    @json($entity, JSON_PRETTY_PRINT);
    </pre>
    @endisset

    @isset($children)
    Children:
    <pre>
    @json($children, JSON_PRETTY_PRINT);
    </pre>
    @endisset
</footer>
</body>
</html>
