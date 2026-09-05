<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign in — {{ config('company.legal_name') }} console</title>
    {{ Vite::fonts() }}
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="adm__auth">
        <div class="adm__authcard">
            <h1>Console sign in</h1>
            <p>{{ config('company.legal_name') }}</p>

            @if (session('status'))
                <p class="adm__flash">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <div class="adm__errors" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.store') }}" class="adm__authform">
                @csrf
                <div class="adm__field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                </div>
                <div class="adm__field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <label class="adm__toggle">
                    <input type="checkbox" name="remember" value="1">
                    <span><b>Stay signed in</b></span>
                </label>
                <button type="submit" class="btn btn--primary">Sign in</button>
            </form>
        </div>
    </div>
</body>
</html>
