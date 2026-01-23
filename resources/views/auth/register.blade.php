<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account – Odovin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-neutral-50 text-gray-900">

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">

        <!-- Brand -->
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-semibold tracking-tight">
                Odovin
            </h1>
            <p class="mt-2 text-sm text-gray-500">
                Create your account to get started
            </p>
        </div>

        <!-- Card -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Full name
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-600 focus:ring-indigo-600"
                        placeholder="John Doe"
                    >
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Email address
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-600 focus:ring-indigo-600"
                        placeholder="you@example.com"
                    >
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-600 focus:ring-indigo-600"
                        placeholder="••••••••"
                    >
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Confirm -->
                <div>
                    <label class="block text-sm font-medium mb-1">
                        Confirm password
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-600 focus:ring-indigo-600"
                        placeholder="••••••••"
                    >
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full bg-gray-900 hover:bg-gray-800 text-white py-2.5 rounded-lg font-medium transition"
                >
                    Create account
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">
                Sign in
            </a>
        </div>

        <div class="mt-8 text-center text-xs text-gray-400">
            © {{ date('Y') }} Odovin
        </div>

    </div>
</div>

</body>
</html>
