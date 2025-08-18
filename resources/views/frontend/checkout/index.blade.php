@extends('frontend.layouts.app')

@section('title', 'Checkout & Payment - ' . $title)

@section('content')
<div class="min-h-screen bg-gray-50 pt-32">
    <div class="max-w-4xl mx-auto px-6">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                @if($type === 'session')
                    Complete Your Session Booking
                @else
                    Complete Your Course Enrollment
                @endif
            </h1>
            <p class="text-lg text-gray-600">
                @if($type === 'session')
                    Secure payment processing for your session with {{ $item->mentor->user->name }}
                @else
                    Secure payment processing for {{ $item->title }}
                @endif
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Item Details -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    @if($type === 'session')
                        Session Details
                    @else
                        Course Details
                    @endif
                </h2>
                
                <div class="space-y-4">
                    <!-- Mentor Info -->
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        @php
                            $photoPath = $item->mentor->photo 
                                ? Storage::url($item->mentor->photo) 
                                : asset('assets/images/user-avatar.png');
                        @endphp
                        <img src="{{ $photoPath }}" 
                             alt="{{ $type === 'session' ? $item->mentor->user->name : $item->mentor->name }}" 
                             class="w-12 h-12 rounded-full object-cover">
                        <div>
                            <h3 class="font-semibold text-gray-900">
                                {{ $type === 'session' ? $item->mentor->user->name : $item->mentor->name }}
                            </h3>
                            <p class="text-sm text-gray-600">{{ $item->category->name }}</p>
                        </div>
                    </div>

                    @if($type === 'session')
                        <!-- Session Info -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Date:</span>
                                <span class="font-semibold">{{ \Carbon\Carbon::parse($item->date)->format('l, F j, Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Time:</span>
                                <span class="font-semibold">
                                    {{ \Carbon\Carbon::parse($item->start_time)->format('g:i A') }} - 
                                    {{ \Carbon\Carbon::parse($item->end_time)->format('g:i A') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Duration:</span>
                                <span class="font-semibold">
                                    @php
                                        $startTime = \Carbon\Carbon::parse($item->start_time);
                                        $endTime = \Carbon\Carbon::parse($item->end_time);
                                        $totalMinutes = $startTime->diffInMinutes($endTime);
                                        $hours = intval($totalMinutes / 60);
                                        $minutes = $totalMinutes % 60;
                                    @endphp
                                    @if($hours > 0 && $minutes > 0)
                                        {{ $hours }}h {{ $minutes }}m
                                    @elseif($hours > 0)
                                        {{ $hours }} hour{{ $hours > 1 ? 's' : '' }}
                                    @else
                                        {{ $minutes }} minute{{ $minutes > 1 ? 's' : '' }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Type:</span>
                                <span class="font-semibold capitalize">{{ $item->type ?? 'online' }}</span>
                            </div>
                        </div>
                    @else
                        <!-- Course Title -->
                        <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                            <h3 class="text-lg font-bold text-gray-900">{{ $item->title }}</h3>
                            @if($item->description)
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ Str::limit($item->description, 150) }}</p>
                            @endif
                        </div>
                        
                        <!-- Course Info -->
                        <div class="space-y-3">
                            @if($item->duration_days)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Duration:</span>
                                <span class="font-semibold">{{ $item->duration_days }} days</span>
                            </div>
                            @endif
                            
                            @if($item->start_date)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Start Date:</span>
                                <span class="font-semibold">{{ $item->start_date->format('M d, Y') }}</span>
                            </div>
                            @endif
                            
                            @if($item->end_date)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">End Date:</span>
                                <span class="font-semibold">{{ $item->end_date->format('M d, Y') }}</span>
                            </div>
                            @endif
                            
                            @if($item->discount > 0)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Original Price:</span>
                                    <span class="font-semibold line-through text-gray-400">${{ number_format($item->price, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Discount:</span>
                                    <span class="font-semibold text-green-600">{{ $item->discount }}% OFF</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Sub Categories -->
                    @if($item->subCategories && count($item->subCategories) > 0)
                        <div>
                            <span class="text-gray-600 text-sm">Topics:</span>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($item->subCategories as $subCategory)
                                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                                        {{ $subCategory->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Form -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Payment Information</h2>
                
                <!-- Total Amount -->
                <div class="bg-purple-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                        <span class="text-2xl font-bold text-purple-600">${{ number_format($amount, 2) }}</span>
                    </div>
                </div>

                <!-- Payment Form -->
                <form action="{{ $type === 'session' ? route('checkout.session.process', $item->id) : route('checkout.course.process', $item->id) }}" method="POST" id="payment-form">
                    @csrf
                    
                    <!-- Cardholder Name -->
                    <div class="mb-6">
                        <label for="cardholder-name" class="block text-sm font-medium text-gray-700 mb-2">
                            Cardholder Name
                        </label>
                        <input type="text" 
                               id="cardholder-name" 
                               name="cardholder_name"
                               value="{{ Auth::user()->name }}"
                               placeholder="Enter cardholder name"
                               required
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <!-- Card Element Container -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Credit or Debit Card
                        </label>
                        <div id="card-element" class="p-3 border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-purple-500 focus-within:border-transparent">
                            <!-- Stripe Card Element will be inserted here -->
                        </div>
                        <div id="card-errors" class="mt-2 text-sm text-red-600" role="alert"></div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            id="submit-button"
                            class="w-full bg-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-purple-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="button-text">Pay ${{ number_format($amount, 2) }}</span>
                        <span id="spinner" class="hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </form>

                <!-- Security Notice -->
                <div class="mt-6 text-center">
                    <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Secure payment powered by Stripe</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stripe Scripts -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    // Initialize Stripe
    const stripe = Stripe('{{ config("services.stripe.publishable_key", "pk_test_your_key_here") }}');
    const elements = stripe.elements();

    // Create card element
    const card = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#424770',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
            invalid: {
                color: '#9e2146',
            },
        },
    });

    // Mount card element
    card.mount('#card-element');

    // Handle form submission
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        
        // Disable submit button
        submitButton.disabled = true;
        buttonText.classList.add('hidden');
        spinner.classList.remove('hidden');

        try {
            // Get cardholder name
            const cardholderName = document.getElementById('cardholder-name').value;
            
            // Create payment method
            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: card,
                billing_details: {
                    name: cardholderName,
                },
            });

            if (error) {
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
                submitButton.disabled = false;
                buttonText.classList.remove('hidden');
                spinner.classList.add('hidden');
                return;
            }

            // Add payment method ID to form
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'payment_method_id');
            hiddenInput.setAttribute('value', paymentMethod.id);
            form.appendChild(hiddenInput);

            // Submit form
            form.submit();

        } catch (error) {
            console.error('Error:', error);
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = 'An unexpected error occurred.';
            submitButton.disabled = false;
            buttonText.classList.remove('hidden');
            spinner.classList.add('hidden');
        }
    });

    // Handle card element errors
    card.addEventListener('change', ({error}) => {
        const displayError = document.getElementById('card-errors');
        if (error) {
            displayError.textContent = error.message;
        } else {
            displayError.textContent = '';
        }
    });
</script>
@endsection
