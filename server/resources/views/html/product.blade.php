@extends('html_layout')
@section('main')
    @include('html.partials.breadcrumbs')
    <h1>Product: {{ $entity->model }}: {{ $entity->title }}</h1>
    <em>Price: {{ $entity->price }}</em>
    {{ $entity->body }}

@endsection
