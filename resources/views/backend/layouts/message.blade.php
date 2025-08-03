<!-- Success and Error Messages -->
@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif
@if(session('danger'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-exclamation-circle mr-2"></i>
            <span>{{ session('danger') }}</span>
        </div>
    </div>
@endif

@if($errors->any())
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
        <div class="flex items-center mb-2">
            <i class="fa-solid fa-exclamation-circle mr-2"></i>
            <span class="font-medium">Please fix the following errors:</span>
        </div>
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif