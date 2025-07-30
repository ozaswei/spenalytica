@extends('spenalytica.layouts.combiner')
@section('customCss')
    /* Background gradient */
    body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(90deg, #28c76f, #0099ff);
    color: white;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    }

    /* Large tab container (dashboard area) */
    .large-tabs {
    width: 95%;
    max-width: 1200px;
    height: 700px;
    overflow-y: auto;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    border-radius: 1rem;
    padding: 1.5rem;
    margin: 2rem auto;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    /* Tab header area */
    .tab-header {
    display: flex;
    justify-content: space-around;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    margin-bottom: 1rem;
    }

    /* Individual tab links */
    .tab-link {
    flex: 1;
    text-align: center;
    cursor: pointer;
    padding: 0.8rem 1rem;
    transition: background 0.3s, color 0.3s;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.8);
    }

    /* Active tab clearly visible */
    .tab-link.active {
    border-bottom: 3px solid #00ffc3;
    color: #00ffc3;
    }

    /* Tab content area */
    .tab-content {
    padding: 1rem;
    text-align: left;
    color: rgba(255, 255, 255, 0.95);
    }

    /* Footer */
    footer {
    text-align: center;
    padding: 1rem;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
    margin-top: auto;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
    .large-tabs {
    height: auto;
    padding: 1rem;
    }
    .tab-header {
    flex-direction: column;
    }
    .tab-link {
    width: 100%;
    text-align: left;
    padding: 0.8rem;
    }
    .tab-link.active {
    border-left: 3px solid #00ffc3;
    border-bottom: none;
    }
    }
@endsection
@section('mainContent')
    <!-- Navbar -->
    @include('spenalytica.layouts.navbar')
    <!-- Homepage Content -->
    <main class="dashboard">
        <div class="tab-container large-tabs">
            <div class="tab-header">
                <button class="tab-link active" data-target="overview">Overview</button>
                <button class="tab-link" data-target="addExpense">Add Expense</button>
                <button class="tab-link" data-target="addIncome">Add Income</button>
                <button class="tab-link" data-target="category">Categories</button>
            </div>

            <div id="overview" class="tab-content active">
                <p>This is your spending overview. (Add charts, summaries, etc.)</p>
            </div>
            <div id="addExpense" class="tab-content">
                <form class="form-group">
                    <input type="text" placeholder="Expense Name" class="form-control" required>
                    <select name="" id="">
                        <option disabled selected >-- Category --</option>
                        <option value=""></option>
                    </select>
                    <input type="text" placeholder="Expense Name" class="form-control" required>
                    <input type="number" placeholder="Amount" required>
                    <button type="submit" class="modal-button">Save Expense</button>
                </form>
            </div>
            <div id="addIncome" class="tab-content">
                <form>
                    <input type="text" placeholder="Income Source" required>
                    <input type="number" placeholder="Amount" required>
                    <button type="submit" class="modal-button">Save Income</button>
                </form>
            </div>
            <div id="category" class="tab-content">
                <form>
                    <input type="text" placeholder="Category Name*" required>
                    <input type="number" placeholder="Amount" required>
                    <button type="submit" class="modal-button">Save Income</button>
                </form>
            </div>
        </div>
    </main>
@endsection
@section('customJavascript')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const tabLinks = document.querySelectorAll(".tab-link");
            const tabContents = document.querySelectorAll(".tab-content");

            tabLinks.forEach(link => {
                link.addEventListener("click", () => {
                    // Remove active from all
                    tabLinks.forEach(item => item.classList.remove("active"));
                    tabContents.forEach(item => item.style.display = "none");

                    // Add active to clicked tab
                    link.classList.add("active");

                    // Show content
                    const target = link.getAttribute("data-target");
                    document.getElementById(target).style.display = "block";
                });
            });

            // Activate first tab
            if (tabLinks.length > 0) {
                tabLinks[0].click();
            }
        });
    </script>
@endsection
