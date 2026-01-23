<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login – Odovin</title>
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
                A single platform to manage, track, and optimize every aspect of your vehicle ownership.
            </p>
        </div>

        <div class="space-y-4 text-slate-300 text-sm">
            <p>✔ Fuel & expense tracking</p>
            <p>✔ Maintenance & reminders</p>
            <p>✔ Smart vehicle insights</p>
            <p>✔ Ownership cost comparison</p>
        </div>

        <div class="text-xs text-slate-500">
            © {{ date('Y') }} Odovin. All rights reserved.
        </div>
    </div>

    <!-- RIGHT: LOGIN FORM -->
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">

            <div class="mb-8">
                <h2 class="text-3xl font-semibold text-gray-900">
                    Welcome back
                </h2>
                <p class="text-gray-500 mt-1">
                    Sign in to continue to your dashboard
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

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
                        autofocus
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

                <!-- Options -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm text-gray-600">
                        <input
                            type="checkbox"
                            name="remember"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <span class="ml-2">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a
                            href="{{ route('password.request') }}"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                        >
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg text-base font-medium transition"
                >
                    Sign in
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-6 text-center text-sm text-gray-500">
                New to Odovin
                <a href="{{ route('register') }}" class="text-indigo-600 font-medium hover:underline">
                    Create an account
                </a>
            </div>

        </div>
    </div>

</div>

</body>
</html>
