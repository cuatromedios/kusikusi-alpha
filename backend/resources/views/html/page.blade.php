@extends('html_layout')
@section('main')
    <h1>{{ $entity->model }}: {{ $entity->title }}</h1>
    <em>{{ $entity->summary }}</em>
    {{ $entity->body }}
<div>
    @forelse ($media as $mediumEntity)
        <img src="{{ $mediumEntity['medium']['thumb'] }}" alt="" />
    @empty
        <em>No children</em>
    @endforelse
</div>
@endsection
