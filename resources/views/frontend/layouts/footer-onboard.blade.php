    <div class="mt-28 flex justify-around text-xs text-gray-400 text-center">
        <h2>© 2025 Skillio Course</h2>
        <div class="relative inline-block text-left">
            <form method="POST" action="{{ route('lang.switch') }}">
                @csrf
                <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')" class="flex items-center gap-1">
                    🌐 {{ strtoupper(app()->getLocale()) }}
                    <span><img src="{{ asset('assets/down-arrow-svgrepo-com.svg') }}" alt="Dropdown arrow" class="w-2 h-2 inline-block" /></span>
                </button>
                <div class="absolute right-0 bottom-full mb-2 w-28 bg-white border rounded shadow-lg hidden z-10">
                    <button type="submit" name="lang" value="en" class="block w-full text-left px-3 py-1 hover:bg-gray-100">ENG</button>
                    <button type="submit" name="lang" value="hr" class="block w-full text-left px-3 py-1 hover:bg-gray-100">HR</button>
                    <button type="submit" name="lang" value="sr" class="block w-full text-left px-3 py-1 hover:bg-gray-100">SR</button>
                    <button type="submit" name="lang" value="sl" class="block w-full text-left px-3 py-1 hover:bg-gray-100">SL</button>
                    <button type="submit" name="lang" value="mk" class="block w-full text-left px-3 py-1 hover:bg-gray-100">MK</button>
                </div>
            </form>
        </div>
    </div>