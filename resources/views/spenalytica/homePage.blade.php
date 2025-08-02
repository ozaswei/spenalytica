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
                <h3>Add Expenses</h3>
                <form class="form-group" method="POST" action="{{ route('addExpense') }}">
                    @csrf
                    <div class="mb-2">
                        <input type="text" placeholder="Name" class="form-control" name="expense"
                            value="{{ old('expense') }}" required>
                    </div>
                    <div class="mb-2">
                        <select name="ecategoryId" id="" class="form-control">
                            <option disabled selected>-- Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="subscription" class="form-label">Subscription : </label>
                        <input type="radio" value="1" name="subscription"> Yes
                        <input type="radio" value="0" name="subscription"> No
                    </div>
                    <div class="input-group mb-2">
                        <input type="number" placeholder="Expense Cost" class="form-control" name="cost" step="any"
                            required>
                    </div>
                    <div class="mb-2">
                        <label for="edescription" class="form-label">Description</label>
                        <textarea name="edescription" id="edescription" cols="30" rows="10" class=form-control>{{ old('edescription') }}</textarea>
                    </div>
                    <div class="mb-2">
                        <button type="submit" class="btn btn-success">Add Expense</button>
                    </div>
                </form>
            </div>
            <div id="addIncome" class="tab-content">
                <h3>Add Income</h3>
                <form class="form-group" method="POST" action="{{ route('addIncome') }}">
                    @csrf
                    <div class="mb-2">
                        <input type="text" placeholder="Income Label" class="form-control" name="label"
                            value="{{ old('label') }}" required>
                    </div>
                    <div class="mb-2">
                        <select name="icategoryId" id="" class="form-control">
                            <option disabled selected>-- Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="idescription" class="form-label">Is it a Monthly Recurring Revenue (MRR) : </label>
                        <input type="radio" value="1" name="mrr"> Yes
                        <input type="radio" value="0" name="mrr"> No
                    </div>
                    <div class="input-group mb-2">
                        <input type="number" placeholder="Revenue" class="form-control" name="revenue" step="any"
                            required>
                    </div>
                    <div class="mb-2">
                        <label for="idescription" class="form-label">Description</label>
                        <textarea name="idescription" id="idescription" cols="30" rows="10" class=form-control>{{ old('idescription') }}</textarea>
                    </div>
                    <div class="mb-2">
                        <button type="submit" class="btn btn-success">Add Income</button>
                    </div>
                </form>
            </div>
            <div id="category" class="tab-content">
                <h3>Add Category</h3>
                <form class="form-group" action="{{ route('addCategory') }}" method="POST">
                    @csrf
                    <div class="input-group mb-2">
                        <input type="text" placeholder="Category Name*" name="categoryName" class="form-control"
                            value="{{ old('categoryName') }}" required>
                    </div>
                    <div class="input-group mb-2">
                        <textarea name="cdescription" id="" cols="30" rows="10" class="form-control"
                            placeholder="Category Description">{{ old('cdescription') }}</textarea>
                    </div>
                    <button type="submit" class="modal-button">Add Category</button>
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
