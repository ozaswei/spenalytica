<nav class="navbar">
    <div class="navbar-left">
        <img src="{{ asset('images/logo.png') }}" alt="Spenalytica Logo" class="navbar-logo">
    </div>
    <div class="navbar-right">
        <span class="username" onclick="toggleDropdown()">Username ▾</span>
        <div id="userDropdown" class="dropdown-content">
            <a href="#">Homepage</a>
            <a href="#">Sign Out</a>
        </div>
    </div>
</nav>