<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">⚙️ Admin Login</h1>
            <p class="text-sm text-gray-500 mt-1">Restricted area</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1 font-medium text-sm">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full border rounded px-3 py-2" required autofocus>
            </div>

            <div>
                <label class="block mb-1 font-medium text-sm">Password</label>
                <input type="password" name="password"
                       class="w-full border rounded px-3 py-2" required>
            </div>

            <label class="inline-flex items-center text-sm">
                <input type="checkbox" name="remember" class="mr-2"> Remember me
            </label>

            <button class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-semibold">
                Login
            </button>
        </form>

        <p class="text-xs text-gray-400 text-center mt-6">
            Customers? <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Login here</a>
        </p>
    </div>

</body>
</html>