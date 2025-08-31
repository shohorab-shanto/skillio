@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1 mt-1']) }}>
        @foreach ((array) $messages as $message)
            <li class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded relative text-sm">
                {{ $message }}
            </li>
        @endforeach
    </ul>
@endif
