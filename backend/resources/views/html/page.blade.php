<h1>{{ $entity->title }}</h1>
<div>
    @forelse ($media as $mediumEntity)
        <img src="{{ $mediumEntity['medium']['thumb'] }}" alt="" />
    @empty
        <em>No children</em>
    @endforelse
</div>
