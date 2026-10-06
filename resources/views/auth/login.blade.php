<x-guest-layout>

    <h1 class="text-xl font-semibold text-gray-900 mb-1">Welcome back</h1>
    <p class="text-sm text-gray-500 mb-6">Log in to your Silva LPG Trading account.</p>

    @if (session('status'))
    <div class="mb-4 bg-status-success-bg border border-green-200 text-status-success text-sm font-medium px-4 py-3 rounded-lg">
        {{ session('status') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-4 bg-status-danger-bg border border-red-200 text-status-danger text-sm px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-medium text-gray-600 mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
        </div>

        <div>
            <label for="password" class="block text-xs font-medium text-gray-600 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-gray-600">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-gray-300 text-maroon-600 focus:ring-maroon-500">
                Remember me
            </label>

            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-sm text-maroon-600 hover:underline">
                Forgot your password?
            </a>
            @endif
        </div>

        <button type="submit" class="w-full bg-maroon-600 hover:bg-maroon-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg shadow-sm transition">
            Log in
        </button>
    </form>

</x-guest-layout>