@extends('html_layout')
@section('main')
    <h1>{{ $entity->model }}: {{ $entity->title }}</h1>
    <em>{{ $entity->summary }}</em>
<div>
    <ul>
        @forelse ($children as $child)
           <li><a href="{{ $child->url }}">{{ $child->title }}</a></li>
        @empty
           <li>No children</li>
        @endforelse
    </ul>
</div>
@endsection
