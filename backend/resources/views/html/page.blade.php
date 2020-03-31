<h1>{{ $entity['content']['en']['title'] }}</h1>
<div>
    @forelse ($media as $mediumEntity)
        <img src="{{ $mediumEntity['medium']['thumb'] }}" alt="" />
    @empty
        <em>No children</em>
    @endforelse
</div>
