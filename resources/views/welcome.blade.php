<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Odovin - Your Complete Vehicle Management Solution</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .feature-card {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="antialiased" x-data="vinLookup()">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span class="text-xl font-bold text-gray-900">Odovin</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-700 hover:text-blue-600 transition">Features</a>
                    <a href="#how-it-works" class="text-gray-700 hover:text-blue-600 transition">How It Works</a>
                    <a href="#services" class="text-gray-700 hover:text-blue-600 transition">Services</a>
                    <a href="#pricing" class="text-gray-700 hover:text-blue-600 transition">Pricing</a>
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 transition">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section with VIN Lookup -->
    <section class="relative pt-32 pb-20 gradient-bg overflow-hidden">
        <!-- Background Decoration -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h1 class="text-5xl md:text-6xl font-extrabold text-white mb-6 leading-tight">
                    Your Complete Vehicle<br/>Management Solution
                </h1>
                <p class="text-xl text-white/90 max-w-3xl mx-auto mb-12">
                    Enter your VIN and let us handle everything - from maintenance schedules to service bookings. 
                    Never miss another oil change or registration renewal.
                </p>
            </div>

            <!-- VIN Lookup Card -->
            <div class="max-w-3xl mx-auto">
                <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12 transform hover:scale-105 transition duration-300">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Discover Your Vehicle</h2>
                        <p class="text-gray-600">Enter your 17-character VIN to get started</p>
                    </div>

                    <form @submit.prevent="lookupVin" class="space-y-6">
                        <div>
                            <label for="vin" class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle Identification Number (VIN)
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="vin"
                                    x-model="vin"
                                    @input="vin = vin.toUpperCase()"
                                    maxlength="17"
                                    placeholder="Enter 17-character VIN (e.g., 1HGBH41JXMN109186)"
                                    class="w-full px-6 py-4 text-lg border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                    :class="{ 'border-red-500': vinError }"
                                >
                                <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                    <span class="text-sm font-medium" :class="vin.length === 17 ? 'text-green-600' : 'text-gray-400'">
                                        <span x-text="vin.length"></span>/17
                                    </span>
                                </div>
                            </div>
                            <p x-show="vinError" x-text="vinError" class="mt-2 text-sm text-red-600" x-cloak></p>
                            <p class="mt-2 text-xs text-gray-500">
                                Find your VIN on your vehicle registration, insurance card, or driver's side door frame
                            </p>
                        </div>

                        <button 
                            type="submit"
                            :disabled="loading || vin.length !== 17"
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-4 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        >
                            <span x-show="!loading">🚗 Lookup My Vehicle</span>
                            <span x-show="loading" class="flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Looking up vehicle...
                            </span>
                        </button>
                    </form>

                    <!-- Vehicle Details (After Lookup) -->
                    <div x-show="vehicleData" x-cloak class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border-2 border-blue-200">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900" x-text="vehicleData?.year + ' ' + vehicleData?.make + ' ' + vehicleData?.model"></h3>
                                <p class="text-gray-600" x-text="vehicleData?.trim"></p>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                ✓ Verified
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <p class="text-sm text-gray-600">Engine</p>
                                <p class="font-semibold text-gray-900" x-text="vehicleData?.engine || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Transmission</p>
                                <p class="font-semibold text-gray-900" x-text="vehicleData?.transmission || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Fuel Type</p>
                                <p class="font-semibold text-gray-900" x-text="vehicleData?.fuel_type || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Body Type</p>
                                <p class="font-semibold text-gray-900" x-text="vehicleData?.specifications?.body_class || 'N/A'"></p>
                            </div>
                        </div>

                        @auth
                            <a :href="'{{ route('vehicles.create') }}?vin=' + vin" 
                               class="block w-full text-center bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                                Add This Vehicle to My Garage
                            </a>
                        @else
                            <a href="{{ route('register') }}" 
                               class="block w-full text-center bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                                Sign Up to Manage This Vehicle
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Trust Badges -->
            <div class="mt-16 flex flex-wrap justify-center items-center gap-8 text-white/80 text-sm">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span>100% Secure</span>
                </div>
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span>Official NHTSA Data</span>
                </div>
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                    </svg>
                    <span>Free to Start</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Everything You Need in One Place</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Stop juggling multiple apps and spreadsheets. odoVin handles it all.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Proactive Maintenance</h3>
                    <p class="text-gray-600">
                        Never miss an oil change or inspection. We track your mileage and send timely reminders.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Expense Tracking</h3>
                    <p class="text-gray-600">
                        Track every penny spent on your vehicle. Categorize expenses and see where your money goes.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Easy Service Booking</h3>
                    <p class="text-gray-600">
                        Find trusted mechanics nearby and book appointments with just a few clicks.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg">
                    <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Smart Reminders</h3>
                    <p class="text-gray-600">
                        Get alerts for registration renewals, insurance expiration, and upcoming services.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg">
                    <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Document Storage</h3>
                    <p class="text-gray-600">
                        Store registration, insurance, and warranty documents safely in one digital location.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card bg-white p-8 rounded-2xl shadow-lg">
                    <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Detailed Reports</h3>
                    <p class="text-gray-600">
                        Generate comprehensive reports on expenses, maintenance history, and vehicle analytics.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600">Get started in just 3 simple steps</p>
            </div>

            <div class="grid md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="w-20 h-20 bg-blue-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Enter Your VIN</h3>
                    <p class="text-gray-600">
                        Simply enter your 17-character VIN and we'll automatically retrieve all your vehicle details.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-purple-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Set Up Your Profile</h3>
                    <p class="text-gray-600">
                        Add your current mileage, upload documents, and let us create your personalized maintenance schedule.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-green-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Relax & Let Us Handle It</h3>
                    <p class="text-gray-600">
                        Receive timely reminders, book services, track expenses, and never worry about vehicle maintenance again.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 gradient-bg">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">
                Ready to Take Control of Your Vehicle?
            </h2>
            <p class="text-xl text-white/90 mb-10">
                Join thousands of car owners who have simplified their vehicle management with odoVin.
            </p>
            <a href="{{ route('register') }}" 
               class="inline-block bg-white text-blue-600 px-10 py-4 rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl transform hover:scale-105 transition duration-200">
                Get Started Free
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="font-bold text-lg mb-4">ODOVIN</h3>
                    <p class="text-gray-400">Your complete vehicle management solution.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#features" class="hover:text-white transition">Features</a></li>
                        <li><a href="#pricing" class="hover:text-white transition">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition">API</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">About</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition">Careers</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2024 ODOVIN. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function vinLookup() {
            return {
                vin: '',
                loading: false,
                vinError: '',
                vehicleData: null,
                
                async lookupVin() {
                    this.vinError = '';
                    this.vehicleData = null;
                    
                    if (this.vin.length !== 17) {
                        this.vinError = 'VIN must be exactly 17 characters';
                        return;
                    }
                    
                    this.loading = true;
                    
                    try {
                            const response = await fetch(`/vehicles/decode-vin/${this.vin}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document
                                        .querySelector('meta[name="csrf-token"]')
                                        .content
                                }
                            });

                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.vehicleData = data.data;
                        } else {
                            this.vinError = data.message || 'Failed to decode VIN. Please check and try again.';
                        }
                    } catch (error) {
                        this.vinError = 'An error occurred. Please try again later.';
                        console.error('VIN Lookup Error:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>