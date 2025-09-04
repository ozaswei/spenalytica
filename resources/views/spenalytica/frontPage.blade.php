@extends('spenalytica.layouts.combiner')

@section('customCss')
    /* Reset & Base Styles */
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    min-height: 100vh; display: flex; flex-direction: column; justify-content: space-between;
    background: linear-gradient(to left, #38b2ac, #4299e1); color: white; text-align: center;
    transition: background 2s ease;
    }

    /* Hero Section */
    .hero { flex: 1; display: flex; justify-content: center; align-items: center; padding: 2rem; }
    .hero-content {
    max-width: 700px; width: 100%; background: rgba(255,255,255,0.08); border-radius: 1rem; padding: 2rem;
    backdrop-filter: blur(10px); box-shadow: 0 8px 30px rgba(0,0,0,0.2); transition: transform 0.3s ease;
    }
    .hero-content:hover { transform: translateY(-5px); }
    .logo { max-width: 200px; width: 100%; height: auto; margin-bottom: 1.5rem; }
    .main-heading { font-size: 2.2rem; font-weight: 700; margin-bottom: 1rem; }
    .hero-subtitle { font-size: 1.1rem; margin-bottom: 1.5rem; line-height: 1.6; }
    .cta-button {
    display: inline-block; background: white; color: #4299e1; padding: .8rem 1.6rem; border-radius: 999px; font-weight: 600;
    text-decoration: none; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: transform .2s ease, box-shadow .2s ease;
    }
    .cta-button:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(0,0,0,0.3); }

    /* Footer */
    .footer { text-align: center; padding: 1rem; font-size: .9rem; background: rgba(0,0,0,0.1); backdrop-filter: blur(5px);
    }

    /* Modal Styles - glassmorphism look */
    .modal {
    display: none; position: fixed; z-index: 100; inset: 0; width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.4); justify-content: center; align-items: center; padding: 1rem;
    }
    .modal-content {
    position: relative;
    background: rgba(255,255,255,0.08); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);
    padding: 2rem; border-radius: 1rem; width: 90%; max-width: 400px; color: white; text-align: center;
    transform: scale(0.8); opacity: 0; transition: transform .3s ease, opacity .3s ease;
    box-shadow: 0 8px 30px rgba(0,0,0,0.3);
    }
    .modal.show .modal-content { transform: scale(1); opacity: 1; }
    .close { position: absolute; top: 10px; right: 20px; font-size: 1.5rem; cursor: pointer; color: white; }

    .tab-header { display: flex; justify-content: center; margin-bottom: 1rem; gap: .25rem; }
    .tab-link {
    flex: 1; padding: .5rem; cursor: pointer; background: none; border: none; font-weight: bold; color: #fff;
    border-bottom: 2px solid transparent; transition: border-bottom .3s, color .3s;
    }
    .tab-link.active { border-bottom: 2px solid #38b2ac; color: #38b2ac; }

    .tab-content { display: none; }
    .tab-content.active { display: block; }

    .modal-content form { display: flex; flex-direction: column; text-align: left; gap: .6rem; }
    .modal-content input {
    padding: .6rem; border: none; border-radius: 5px; background: rgba(255,255,255,0.15); color: white;
    outline: none; transition: background .3s;
    }
    .modal-content input::placeholder { color: rgba(255,255,255,0.7); }
    .modal-content input:focus { background: rgba(255,255,255,0.25); }

    .modal-button {
    margin-top: .4rem; background: #38b2ac; color: white; padding: .6rem; border: none; border-radius: 5px;
    cursor: pointer; font-weight: 600; transition: background .3s;
    }
    .modal-button:hover { background: #2c998e; }

    /* Alerts */
    .alert {
    text-align: left; background: rgba(255, 80, 80, 0.18); border: 1px solid rgba(255,80,80,0.35);
    color: #ffe0e0; padding: .6rem .75rem; border-radius: .5rem; margin-bottom: .8rem; font-size: .95rem;
    }
    .alert-success {
    background: rgba(56, 178, 172, 0.18); border-color: rgba(56,178,172,0.35); color: #e3fffb;
    }

    /* Responsive tweaks */
    @media (max-width: 768px) {
    .main-heading { font-size: 1.8rem; }
    .hero-subtitle { font-size: 1rem; }
    }
@endsection

@section('mainContent')
    <header class="hero">
        <div class="hero-content">
            <img src="{{ asset('images/logo.png') }}" alt="Spenalytica Logo" class="logo">
            <h1 class="main-heading">Spend smarter with Spenalytica</h1>
            <p class="hero-subtitle">Track your spending, analyze your habits, and save smarter. Spend better. Live better.
            </p>
            <a href="#" class="cta-button" onclick="openModal()">Get Started</a>
        </div>
    </header>

    <!-- Modal -->
    @php
        // Decide which tab to show if there are messages/errors.
        // We use a hidden "form" input in each form to remember which tab was submitted.
        $initialTab = old('form') ?: (session('status') ? 'forgot' : 'signin');
        $shouldOpen = $errors->any() || session('status'); // open modal if any errors or status messages exist
    @endphp

    <div id="authModal" class="modal {{ $shouldOpen ? 'show' : '' }}" style="{{ $shouldOpen ? 'display:flex' : '' }}">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>

            <div class="tab-header">
                <button class="tab-link {{ $initialTab === 'signin' ? 'active' : '' }}"
                    onclick="openTab(event, 'signin')">Sign In</button>
                <button class="tab-link {{ $initialTab === 'register' ? 'active' : '' }}"
                    onclick="openTab(event, 'register')">Register</button>
                <button class="tab-link {{ $initialTab === 'forgot' ? 'active' : '' }}"
                    onclick="openTab(event, 'forgot')">Forgot Password</button>
            </div>

            {{-- Sign In --}}
            <div id="signin" class="tab-content {{ $initialTab === 'signin' ? 'active' : '' }}">
                <h2 style="margin-bottom:10px">Sign In</h2>

                {{-- Wrong password / invalid credentials message (Laravel usually returns it on 'email') --}}
                @if (old('form') === 'signin' && $errors->has('email'))
                    <div class="alert">
                        {{ $errors->first('email') }}
                    </div>
                @endif

                {{-- Display any other login-related errors when Sign In was submitted --}}
                @if (old('form') === 'signin' && $errors->any() && !$errors->has('email'))
                    <div class="alert">
                        @foreach ($errors->all() as $e)
                            <div>{{ $e }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="form" value="signin">
                    <input type="email" placeholder="Email" name="email" value="{{ old('email') }}" required
                        autocomplete="email">
                    <input type="password" placeholder="Password" name="password" required autocomplete="current-password">
                    <button type="submit" class="modal-button">Sign In</button>
                </form>
            </div>

            {{-- Register --}}
            <div id="register" class="tab-content {{ $initialTab === 'register' ? 'active' : '' }}">
                <h2 style="margin-bottom:10px">Register</h2>

                @if (old('form') === 'register' && $errors->any())
                    <div class="alert">
                        @foreach ($errors->all() as $e)
                            <div>{{ $e }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <input type="hidden" name="form" value="register">
                    <input type="text" placeholder="Name" name="name" value="{{ old('name') }}" required
                        autocomplete="name">
                    <input type="email" placeholder="Email" name="email" value="{{ old('email') }}" required
                        autocomplete="email">
                    <input type="password" placeholder="Password" name="password" required autocomplete="new-password">
                    <input type="password" placeholder="Confirm Password" name="password_confirmation" required
                        autocomplete="new-password">
                    <button type="submit" class="modal-button">Register</button>
                </form>
            </div>

            {{-- Forgot Password --}}
            <div id="forgot" class="tab-content {{ $initialTab === 'forgot' ? 'active' : '' }}">
                <h2 style="margin-bottom:10px">Forgot Password</h2>

                {{-- Success message when reset email sent --}}
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Show errors only when this tab was submitted --}}
                @if (old('form') === 'forgot' && $errors->any())
                    <div class="alert">
                        @foreach ($errors->all() as $e)
                            <div>{{ $e }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <input type="hidden" name="form" value="forgot">
                    <input type="email" placeholder="Enter your email" name="email" value="{{ old('email') }}"
                        required autocomplete="email">
                    <button type="submit" class="modal-button">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('customJavascript')
    <script>
        function openModal(tab = 'signin') {
            const modal = document.getElementById("authModal");
            modal.style.display = "flex";
            requestAnimationFrame(() => modal.classList.add("show"));
            // ensure correct tab is shown when opened programmatically
            const btn = document.querySelector(`.tab-header .tab-link[onclick*="${tab}"]`);
            if (btn) btn.click();
        }

        function closeModal() {
            const modal = document.getElementById("authModal");
            modal.classList.remove("show");
            setTimeout(() => {
                modal.style.display = "none";
            }, 300);
        }

        // Switch tabs
        function openTab(evt, tabName) {
            const tabcontent = document.getElementsByClassName("tab-content");
            const tablinks = document.getElementsByClassName("tab-link");
            for (let i = 0; i < tabcontent.length; i++) tabcontent[i].classList.remove("active");
            for (let i = 0; i < tablinks.length; i++) tablinks[i].classList.remove("active");
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById("authModal");
            if (event.target === modal) closeModal();
        });

        // Auto-open modal & correct tab if server said so
        document.addEventListener('DOMContentLoaded', () => {
            const shouldOpen = {{ $shouldOpen ? 'true' : 'false' }};
            const initialTab = @json($initialTab);
            if (shouldOpen) openModal(initialTab);
        });
    </script>
@endsection
