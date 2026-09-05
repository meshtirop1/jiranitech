<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Create administrator — {{ config('company.legal_name') }}</title>
    {{ Vite::fonts() }}
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="adm__auth">
        <div class="adm__authcard">
            <h1>Create the first administrator</h1>
            <p>
                This page works once. As soon as an administrator exists it returns a 404,
                so there is no standing registration endpoint on a public site.
            </p>

            @if ($errors->any())
                <div class="adm__errors" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.setup.store') }}" class="adm__authform">
                @csrf
                <div class="adm__field">
                    <label for="name">Your name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
                </div>
                <div class="adm__field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                </div>
                <div class="adm__field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password">
                    <small>At least 12 characters, with letters, numbers and symbols. Choose it yourself and store it in a password manager.</small>
                </div>
                <div class="adm__field">
                    <label for="password_confirmation">Confirm password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn--primary">Create administrator</button>
            </form>
        </div>
    </div>
</body>
</html>
