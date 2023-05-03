<x-mail::message>
# {{$post->title}}

{{ $post->description }}

<x-mail::button :url="''">
Link to post
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
