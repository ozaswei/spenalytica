@extends('spenalytica.layouts.combiner')
@section('customCss')
    /* Reset & Base Styles */
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    }

    body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: linear-gradient(to left, #38b2ac, #4299e1);
    /* teal green to blue */
    color: white;
    text-align: center;
    transition: background 2s ease;
    /* smooth transition effect if you later change bg */
    }

    /* Hero Section */
    .hero {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    }

    .hero-content {
    max-width: 700px;
    width: 100%;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 1rem;
    padding: 2rem;
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
    }

    .hero-content:hover {
    transform: translateY(-5px);
    }

    .logo {
    max-width: 200px;
    width: 100%;
    height: auto;
    margin-bottom: 1.5rem;
    }

    .main-heading {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 1rem;
    }

    .hero-subtitle {
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    line-height: 1.6;
    }

    .cta-button {
    display: inline-block;
    background: white;
    color: #4299e1;
    padding: 0.8rem 1.6rem;
    border-radius: 999px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .cta-button:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    /* Footer */
    .footer {
    text-align: center;
    padding: 1rem;
    font-size: 0.9rem;
    background: rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(5px);
    }

    /* Modal Styles - glassmorphism look */
    .modal {
    display: none;
    position: fixed;
    z-index: 100;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    /* slightly softer backdrop */
    justify-content: center;
    align-items: center;
    padding: 1rem;
    }

    .modal-content {
    background: rgba(255, 255, 255, 0.08);
    /* translucent glass effect */
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    padding: 2rem;
    border-radius: 1rem;
    width: 90%;
    max-width: 400px;
    color: white;
    /* match hero text color */
    text-align: center;
    transform: scale(0.8);
    opacity: 0;
    transition: transform 0.3s ease, opacity 0.3s ease;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
    }

    .modal.show .modal-content {
    transform: scale(1);
    opacity: 1;
    }

    .close {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 1.5rem;
    cursor: pointer;
    color: white;
    }

    .tab-header {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
    }

    .tab-link {
    flex: 1;
    padding: 0.5rem;
    cursor: pointer;
    background: none;
    border: none;
    font-weight: bold;
    color: #fff;
    border-bottom: 2px solid transparent;
    transition: border-bottom 0.3s, color 0.3s;
    }

    .tab-link.active {
    border-bottom: 2px solid #38b2ac;
    /* teal accent from background */
    color: #38b2ac;
    }

    .tab-content {
    display: none;
    }

    .tab-content.active {
    display: block;
    }

    .modal-content form {
    display: flex;
    flex-direction: column;
    }

    .modal-content input {
    margin-bottom: 1rem;
    padding: 0.6rem;
    border: none;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.15);
    color: white;
    outline: none;
    transition: background 0.3s;
    }

    .modal-content input::placeholder {
    color: rgba(255, 255, 255, 0.7);
    }

    .modal-content input:focus {
    background: rgba(255, 255, 255, 0.25);
    }

    .modal-button {
    background: #38b2ac;
    color: white;
    padding: 0.6rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s;
    }

    .modal-button:hover {
    background: #2c998e;
    }


    /* Responsive tweaks */
    @media (max-width: 768px) {
    .main-heading {
    font-size: 1.8rem;
    }

    .hero-subtitle {
    font-size: 1rem;
    }
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
    <div id="authModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="tab-header">
                <button class="tab-link active" onclick="openTab(event, 'signin')">Sign In</button>
                <button class="tab-link" onclick="openTab(event, 'register')">Register</button>
                <button class="tab-link" onclick="openTab(event, 'forgot')">Forgot Password</button>
            </div>
            <div id="signin" class="tab-content active">
                <h2 style="margin-bottom: 10px">Sign In</h2>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="email" placeholder="Email" name="email" :value="old('email')" required>
                    <input type="password" placeholder="Password" name="password" required>
                    <button type="submit" class="modal-button">Sign In</button>
                </form>
            </div>
            <div id="register" class="tab-content">
                <h2 style="margin-bottom: 10px">Register</h2>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <input type="text" placeholder="Name" name="name" :value="old('name')" required>
                    <input type="email" placeholder="Email" name="email" :value="old('email')" required>
                    <input type="password" placeholder="Password" name="password" required>
                    <input type="password" placeholder="Confirm Password" name="password_confirmation" required>
                    <button type="submit" class="modal-button">Register</button>
                </form>
            </div>
            <div id="forgot" class="tab-content">
                <h2 style="margin-bottom: 10px">Forgot Password</h2>
                <form>
                    @csrf
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit" class="modal-button">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('customJavascript')
    <script>
        function openModal() {
            let modal = document.getElementById("authModal");
            modal.style.display = "flex";
            setTimeout(() => {
                modal.classList.add("show");
            }, 10); // delay to trigger transition
        }

        function closeModal() {
            let modal = document.getElementById("authModal");
            modal.classList.remove("show");
            setTimeout(() => {
                modal.style.display = "none";
            }, 300); // wait for animation to finish
        }

        // Switch tabs
        function openTab(evt, tabName) {
            let tabcontent = document.getElementsByClassName("tab-content");
            let tablinks = document.getElementsByClassName("tab-link");

            for (let i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }
            for (let i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }

            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }


        // Close modal when clicking outside
        window.onclick = function(event) {
            let modal = document.getElementById("authModal");
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
@endsection
