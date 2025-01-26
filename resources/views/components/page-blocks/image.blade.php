@props(['content'])

<figure class="my-4">
    <img src="{{ asset('storage/' . $content) }}" alt="Image" class="w-full h-auto rounded-lg" />
</figure>
