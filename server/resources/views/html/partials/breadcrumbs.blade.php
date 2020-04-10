<ol>
@foreach($ancestors as $ancestor)
        <li><a href="{{ $ancestor->route->path }}">{{ $ancestor->title }}</a></li>
@endforeach
</ol>
