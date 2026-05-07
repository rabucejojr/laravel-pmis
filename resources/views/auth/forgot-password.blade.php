<x-guest-layout>
    {{-- Heading --}}
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-primary">Forgot your password?</h2>
        <p class="text-xs text-gray-500 mt-1">Enter your registered email and we'll send you a reset link.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-semibold text-gray-600 mb-1">Email Address</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </span>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="you@dost.gov.ph"
                       required autofocus autocomplete="username"
                       class="w-full pl-9 pr-3 py-2 rounded-lg border bg-white text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}">
            </div>
            @error('email')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-2.5 rounded-lg text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
            Send Password Reset Link
        </button>
    </form>

    {{-- Back to login --}}
    <p class="mt-6 text-center text-xs text-gray-500">
        Remembered your password?
        <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Sign in</a>
    </p>
</x-guest-layout>
