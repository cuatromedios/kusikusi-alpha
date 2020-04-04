@extends('html_layout')
@section('main')
    <h1>{{ $entity['content']['title'][$lang] }}</h1>
    <p>{{ $entity->welcome }}</p>
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
