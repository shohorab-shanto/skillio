<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-md transition duration-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
