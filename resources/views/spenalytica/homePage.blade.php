@extends('spenalytica.layouts.combiner')

@section('customCss')
    /* Background gradient & layout */
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

    .large-tabs {
    width: 100%; /* Full width */
    max-width: 100%; /* Remove width cap */
    height: calc(100vh - 120px); /* Full height minus navbar+footer (adjust number as needed) */
    overflow-y: auto;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    border-radius: 0; /* Remove radius for edge-to-edge */
    padding: 1.5rem;
    margin: 0 auto; /* Center horizontally */
    box-shadow: none; /* Optional: remove box shadow if you want full edge look */
    }

    .tab-header {
    display: flex;
    justify-content: space-around;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    margin-bottom: 1rem;
    }

    .tab-link {
    flex: 1;
    text-align: center;
    cursor: pointer;
    padding: 0.8rem 1rem;
    transition: background 0.3s, color 0.3s;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.8);
    background: none;
    border: none;
    }

    .tab-link.active {
    border-bottom: 3px solid #00ffc3;
    color: #00ffc3;
    }

    .tab-content {
    display: none;
    padding: 1rem;
    text-align: left;
    color: rgba(255, 255, 255, 0.95);
    }

    .tab-content.active {
    display: block;
    }

    footer {
    text-align: center;
    padding: 1rem;
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.7);
    margin-top: auto;
    }

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
    @include('spenalytica.layouts.navbar')

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
                <div class="card">
                    <div class="card-header">
                        Add Expenses
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('addExpense') }}">
                            @csrf
                            <div class="mb-2">
                                <input type="text" name="expense" placeholder="Name" class="form-control"
                                    value="{{ old('expense') }}" required>
                            </div>
                            <div class="mb-2">
                                <select name="ecategoryId" class="form-control" required>
                                    <option disabled selected>-- Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Subscription:</label>
                                <input type="radio" value="1" name="subscription"> Yes
                                <input type="radio" value="0" name="subscription"> No
                            </div>
                            <div class="mb-2">
                                <input type="number" name="cost" placeholder="Expense Cost" class="form-control"
                                    step="any" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Description</label>
                                <textarea name="edescription" cols="30" rows="5" class="form-control">{{ old('edescription') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Add Expense</button>
                        </form>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">All Expenses Added</div>
                    <div class="card-body">
                        <table id="expenseTable" class="display">
                            <thead>
                                <tr>
                                    <th>Expense</th>
                                    <th>Category</th>
                                    <th>Cost</th>
                                    <th>Added at</th>
                                    <th>Updated at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->expense }}</td>
                                        <td>{{ $expense->category->category }}</td>
                                        <td>{{ $expense->cost }}</td>
                                        <td>{{ $expense->created_at }}</td>
                                        <td>{{ $expense->updated_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="addIncome" class="tab-content">
                <div class="card">
                    <div class="card-header">Add Income</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('addIncome') }}">
                            @csrf
                            <div class="mb-2">
                                <input type="text" name="label" placeholder="Income Label" class="form-control"
                                    value="{{ old('label') }}" required>
                            </div>
                            <div class="mb-2">
                                <select name="icategoryId" class="form-control" required>
                                    <option disabled selected>-- Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Is it a Monthly Recurring Revenue (MRR):</label>
                                <input type="radio" value="1" name="mrr"> Yes
                                <input type="radio" value="0" name="mrr"> No
                            </div>
                            <div class="mb-2">
                                <input type="number" name="revenue" placeholder="Revenue" class="form-control"
                                    step="any" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Description</label>
                                <textarea name="idescription" cols="30" rows="5" class="form-control">{{ old('idescription') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Add Income</button>
                        </form>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">All Income Revenues Added</div>
                    <div class="card-body">
                        <table id="incomeTable" class="display">
                            <thead>
                                <tr>
                                    <th>Income</th>
                                    <th>Category</th>
                                    <th>Cost</th>
                                    <th>Added at</th>
                                    <th>Updated at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($incomes as $income)
                                    <tr>
                                        <td>{{ $income->label }}</td>
                                        <td>{{ $income->category->category }}</td>
                                        <td>{{ $income->revenue }}</td>
                                        <td>{{ $income->created_at }}</td>
                                        <td>{{ $income->updated_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="category" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        Add Category
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('addCategory') }}">
                            @csrf
                            <div class="mb-2">
                                <input type="text" name="categoryName" placeholder="Category Name*"
                                    class="form-control" value="{{ old('categoryName') }}" required>
                            </div>
                            <div class="mb-2">
                                <textarea name="cdescription" cols="30" rows="5" class="form-control"
                                    placeholder="Category Description">{{ old('cdescription') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Add Category</button>
                        </form>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-header">
                        All Categories added
                    </div>
                    <div class="card-body">
                        <table id="categoryTable" class="display">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Added at</th>
                                    <th>Updated at</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->category }}</td>
                                        <td>{{ $category->created_at }}</td>
                                        <td>{{ $category->updated_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
                    tabLinks.forEach(item => item.classList.remove("active"));
                    tabContents.forEach(item => item.classList.remove("active"));

                    link.classList.add("active");
                    const target = link.getAttribute("data-target");
                    document.getElementById(target).classList.add("active");
                });
            });
        });
        //data table
        $(document).ready(function() {
            $('#expenseTable').DataTable();
        });
        $(document).ready(function() {
            $('#incomeTable').DataTable();
        });
        $(document).ready(function() {
            $('#categoryTable').DataTable();
        });
    </script>
@endsection
