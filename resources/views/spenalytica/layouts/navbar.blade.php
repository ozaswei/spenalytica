<nav class="navbar d-flex flex-wrap justify-content-between align-items-center px-3 py-2">
    <div class="navbar-left mb-2 mb-md-0">
        <img src="{{ asset('images/logo.png') }}" alt="Spenalytica Logo" class="navbar-logo" style="max-height: 40px;">
    </div>
    <div class="navbar-right d-flex flex-wrap align-items-center justify-content-center">
        <span class="me-3 mb-2 mb-md-0 text-nowrap">Hi, {{ Auth::user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}" class="mb-0 ms-2">
            @csrf
            <button type="submit" class="btn btn-light btn-sm p-0">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</nav>
