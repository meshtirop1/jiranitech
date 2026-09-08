<x-layouts.erp title="Sign in">
    <div class="erp__auth">
        <h1>Delivery sign in</h1>
        <p class="erp__sub">{{ config('company.legal_name') }}</p>

        <form method="POST" action="{{ route('erp.login.store') }}" class="erp__form">
            @csrf
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required autofocus autocomplete="username" value="{{ old('email') }}">

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">

            <label class="erp__check">
                <input type="checkbox" name="remember" value="1"> Stay signed in
            </label>

            <button type="submit" class="erp__btn">Sign in</button>
        </form>

        <p class="erp__muted erp__authnote">Accounts are created by your team's leadership. There is no self-registration.</p>
    </div>
</x-layouts.erp>
