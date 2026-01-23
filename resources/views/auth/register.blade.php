<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account – Odovin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex">

    <!-- LEFT: BRAND / VALUE -->
    <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-slate-900 to-slate-800 text-white p-12 flex-col justify-between">
        <div>
            <h1 class="text-4xl font-bold tracking-tight">
                Odovin
            </h1>
            <p class="mt-4 text-lg text-slate-300 max-w-md">
                Take control of your vehicle ownership from day one.
            </p>
        </div>

        <div class="space-y-4 text-slate-300 text-sm">
            <p>✔ One account for all your vehicles</p>
            <p>✔ Smart maintenance & reminders</p>
            <p>✔ Fuel, expenses & comparisons</p>
            <p>✔ Ownership insights that save money</p>
        </div>

        <div class="text-xs text-slate-500">
            © {{ date('Y') }} Odovin. All rights reserved.
        </div>
    </div>

    <!-- RIGHT: REGISTER FORM -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">

            <div class="mb-8">
                <h2 class="text-3xl font-semibold text-gray-900">
                    Create your account
                </h2>
                <p class="text-gray-500 mt-1">
                    Start managing your vehicles smarter
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Full name
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="John Doe"
                    >
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email address
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="you@example.com"
                    >
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="••••••••"
                    >
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm password
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="••••••••"
                    >
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg text-base font-medium transition"
                >
                    Create account
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-6 text-center text-sm text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:underline">
                    Sign in
                </a>
            </div>

        </div>
    </div>

</div>

</body>
</html>
