@extends('frontend.layouts.app')

@section('title', 'Security Capabilities - Skillio')

@section('content')
<div class="min-h-screen bg-gray-50 pt-32 pb-5">
    <div class="max-w-[1400px] mx-auto px-6">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Security Capabilities and Policy</h1>
            <h2 class="text-xl md:text-2xl font-semibold text-gray-700 mb-4">For Transmission of Credit Card Details</h2>
            <div class="flex justify-center items-center gap-4 text-sm text-gray-600 mt-4">
                <div class="flex items-center gap-2">
                    <span class="font-medium">Effective Date:</span>
                    <span>September 10, 2025</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            
            <!-- What Information Do We Collect -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">What Information Do We Collect and How Do We Use It?</h2>
                <p class="text-gray-700 leading-relaxed">
                    When you place an order on our website, we collect personal information such as your name, email address, 
                    phone number, billing and shipping address, and payment details (if paying by card). This information is 
                    necessary to process your order, issue invoices, deliver products or services, and provide customer support.
                </p>
            </div>

            <!-- How Do We Protect Customer Information -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">How Do We Protect Customer Information?</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p>We take the security of your information seriously. Our website uses industry-standard Secure Socket Layer (SSL) encryption to protect data during transmission.</p>
                    <p>All sensitive data entered on our site, including credit card information, is encrypted and securely transmitted to our payment processor.</p>
                    <p><strong>We do not store credit card numbers or CVV codes on our servers.</strong></p>
                    <p>In addition, our website is monitored and protected using up-to-date security technologies, including firewalls, anti-malware tools, and intrusion prevention systems.</p>
                    <p>Access to customer data is restricted and controlled through strict internal procedures.</p>
                </div>
            </div>

            <!-- Payment Gateway Info -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Payment Gateway Info</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p>We partner with trusted and PCI-DSS compliant payment gateways (such as Stripe or PayPal) to handle all transactions.</p>
                    <p><strong>Your credit card details are securely processed by the payment provider and are never stored or accessible by us.</strong></p>
                    <p>This ensures that every transaction is encrypted, authenticated, and conducted under strict security standards.</p>
                    <p>If you have any concerns about payment security, please contact us directly for more information.</p>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="mb-8">
                <p class="text-gray-700 leading-relaxed mb-4">
                    If you have any concerns about payment security, please contact us directly for more information.
                </p>
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Skillio LLC</strong></p>
                        <p>30 N Gould St, Sheridan, WY 82801</p>
                        <p>Email: <a href="mailto:info@skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">info@skillio.pro</a></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
