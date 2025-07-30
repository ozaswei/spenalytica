<nav class="navbar">
    <div class="navbar-left">
        <img src="{{ asset('images/logo.png') }}" alt="Spenalytica Logo" class="navbar-logo">
    </div>
    <div class="navbar-right">
        <span class="username" onclick="toggleDropdown()">{{ Auth::user()->name }} ▾</span>
        <div id="userDropdown" class="dropdown-content">
            <a href="#">Homepage</a>
            <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
    </div>
</nav>