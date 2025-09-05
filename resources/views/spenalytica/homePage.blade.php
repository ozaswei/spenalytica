@extends('spenalytica.layouts.combiner')

@section('customCss')
    :root {
    --brand-green: #28c76f;
    --brand-blue: #0099ff;
    --text-light: #fff;
    --text-dark: #222;
    --bg-light: #f2fbfc;
    --border-light: #cce4fa;
    --muted: #a3babb;
    }

    *,
    *::before,
    *::after {
    box-sizing: border-box;
    }

    body {
    margin: 0;
    padding: 0;
    font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(120deg, var(--brand-green) 0%, var(--brand-blue) 100%);
    color: var(--text-light);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    }

    .navbar {
    background: rgba(0,0,0,0.15);
    padding: 1rem 2rem;
    border-bottom: 1px solid rgba(0,0,0,0.08);
    }

    .large-tabs {
    width: 100%;
    padding: 2rem;
    }

    .tab-header {
    display: flex;
    border-bottom: 2px solid rgba(255,255,255,0.15);
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    }

    .tab-link {
    background: none;
    color: #d6f8ff;
    border: none;
    font-weight: 600;
    font-size: 1.15rem;
    padding: 1rem 2rem;
    border-radius: 12px 12px 0 0;
    cursor: pointer;
    transition: background 0.18s, color 0.18s;
    }
    .tab-link.active {
    color: var(--brand-blue);
    background: var(--text-light);
    border-bottom: 3px solid var(--brand-green);
    box-shadow: 0 2px 8px 0 rgba(0,0,0,0.06);
    }

    .tab-content {
    display: none;
    padding: 1rem 0.5rem;
    color: var(--text-light);
    min-height: 100px;
    }
    .tab-content.active {
    display: block;
    animation: fadeIn 0.45s;
    }
    @keyframes fadeIn {
    from {opacity:0; transform:translateY(16px);}
    to {opacity: 1; transform: none;}
    }

    .card {
    background: rgba(255,255,255,0.16);
    border-radius: 12px;
    padding: 1.5rem 1.2rem;
    box-shadow: 0 4px 20px 0 rgba(0,0,0,0.09);
    margin-bottom: 1.5rem;
    }

    footer {
    text-align: center;
    padding: 1.2rem 0;
    background: rgba(0,0,0,0.10);
    color: #d8f6ed;
    font-size: 1rem;
    margin-top: auto;
    border-top: 1px solid rgba(255,255,255,0.04);
    }

    input, select, textarea {
    border-radius: 8px;
    border: 1px solid var(--border-light);
    padding: 0.8rem 0.9rem;
    background: var(--bg-light);
    color: var(--text-dark);
    font-size: 1.06rem;
    margin-bottom: 0.7rem;
    transition: border 0.18s, background 0.18s;
    }
    input:focus, select:focus, textarea:focus {
    outline: none;
    border: 1.5px solid var(--brand-blue);
    background: #fff;
    }
    ::-webkit-input-placeholder { color: var(--muted); }
    :-moz-placeholder { color: var(--muted); }
    ::-moz-placeholder { color: var(--muted); }
    :-ms-input-placeholder { color: var(--muted); }
    ::placeholder { color: var(--muted); }

    .btn {
    border-radius: 8px;
    font-weight: 600;
    letter-spacing: .01em;
    transition: background 0.14s, color 0.14s, box-shadow 0.1s;
    padding: 0.7rem 1.3rem;
    min-width: 90px;
    }
    .btn-success {
    background: var(--brand-green);
    color: var(--text-light);
    border: none;
    }
    .btn-primary {
    background: var(--brand-blue);
    color: var(--text-light);
    border: none;
    }
    .btn-danger {
    background: #f44336;
    color: var(--text-light);
    border: none;
    }
    .btn:hover, .btn:focus {
    opacity: 0.90;
    box-shadow: 0 2px 8px rgba(0,0,0,0.16);
    }

    /* Chart containers */
    .chart-container {
    height: 340px;
    }
    .chart-container canvas {
    height: 340px !important;
    width: 100% !important;
    max-width: 100%;
    max-height: 100%;
    }

    /* Tables */
    table {
    width: 100%;
    background: rgba(255,255,255,0.10);
    border-radius: 8px;
    border-collapse: collapse;
    color: var(--text-dark);
    overflow-x: auto;
    margin-bottom: 1.5rem;
    }
    thead {
    background: linear-gradient(90deg, var(--brand-blue) 20%, var(--brand-green) 100%);
    color: #fff;
    }
    tbody tr:nth-of-type(even) {
    background: rgba(0,0,0,0.03);
    }
    th, td {
    padding: 0.8rem 0.7rem;
    text-align: left;
    font-size: 0.97rem;
    }
    th {
    font-weight: 600;
    }

    /* Responsive Design */
    @media (max-width: 900px) {
    .large-tabs {
    padding: 1rem;
    }
    .tab-link {
    font-size: 1rem;
    padding: 0.8rem 1rem;
    }
    }
    @media (max-width: 768px) {
    .large-tabs {
    max-width: 100%;
    margin: 0;
    border-radius: 0;
    padding: 1rem 0;
    }
    .tab-header {
    flex-direction: column;
    gap: 0;
    }
    .tab-link {
    width: 100%;
    border-radius: 0;
    border: none;
    text-align: left;
    }
    .tab-link.active {
    border-left: 3px solid var(--brand-blue);
    background: rgba(255,255,255,0.12);
    }
    .card {
    border-radius: 10px;
    padding: 1rem;
    }
    }
    @media (max-width: 600px) {
    table, thead, tbody, th, td, tr {
    display: block;
    width: 100%;
    }
    th, td {
    padding: 0.7rem 0.4rem;
    font-size: 0.95rem;
    }
    thead {
    display: none;
    }
    tbody tr {
    margin-bottom: 1.2rem;
    border-bottom: 1px solid #eee;
    background: rgba(255,255,255,0.16);
    border-radius: 7px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
    td {
    display: flex;
    justify-content: space-between;
    position: relative;
    padding-left: 40%;
    min-height: 36px;
    font-size: 1rem;
    }
    td:before {
    content: attr(data-label);
    position: absolute;
    left: 0;
    top: 0;
    width: 36%;
    padding-left: 8px;
    color: var(--brand-blue);
    font-weight: 700;
    font-size: 0.92rem;
    }
    }
    /* Budget card tweaks */
    #budgetTable .progress {
    background-color: rgba(255,255,255,0.12);
    }
    #budgetTable .progress-bar {
    background-image: linear-gradient(45deg, rgba(255,255,255,0.2), rgba(255,255,255,0.05));
    color: #000;
    font-size: 12px;
    line-height: 14px;
    }

    /* Make modals slightly brighter for content clarity */
    .modal-content.bg-light.text-dark {
    background: #f8f9fa;
    color: #111;
    }

    /* Small responsive tweak */
    @media (max-width: 576px) {
    #budgetTable td, #budgetTable th {
    font-size: 13px;
    padding: .4rem .6rem;
    }
    .progress { height:12px; }
    }

    .chart-container canvas { display: block; } /* avoid baseline gap */
@endsection

@section('mainContent')
    @include('spenalytica.layouts.navbar')

    <main class="dashboard">
        <div class="tab-container large-tabs">
            <div class="tab-header">
                <button class="tab-link active" data-target="overview">Overview</button>
                <button class="tab-link" data-target="addExpense">Add Expense</button>
                <button class="tab-link" data-target="addIncome">Add Income</button>
                <button class="tab-link" data-target="budget">Budgets</button>
                <button class="tab-link" data-target="category">Categories</button>
            </div>

            <div id="overview" class="tab-content active">
                <!-- Health Snapshot card (place in the overview tab) -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                            <div class="flex-grow-1">
                                <h5 class="mb-1">Health Snapshot</h5>
                                <p class="mb-2 text-muted small">Quick financial health summary using your latest month and
                                    averages.</p>

                                <div class="d-flex flex-wrap gap-3">
                                    <div>
                                        <strong>Net balance</strong>
                                        <div>${{ number_format($currentBalance, 2) }}</div>
                                    </div>

                                    <div>
                                        <strong>Avg monthly Δ</strong>
                                        <div>
                                            @if ($avgSavings === null)
                                                -
                                            @else
                                                ${{ number_format($avgSavings, 2) }}
                                                <small class="text-muted">/ month</small>
                                            @endif
                                        </div>
                                    </div>

                                    <div>
                                        <strong>Latest health</strong>
                                        <div>
                                            @php
                                                $badgeColor = match ($spendingHealth) {
                                                    'Healthy' => 'success',
                                                    'Neutral' => 'secondary',
                                                    'At Risk' => 'warning',
                                                    'Unhealthy' => 'danger',
                                                    'Critical' => 'danger',
                                                    default => 'light',
                                                };
                                            @endphp
                                            <span
                                                class="badge bg-{{ $badgeColor }} text-dark">{{ $spendingHealth }}</span>
                                        </div>
                                    </div>

                                    <div>
                                        <strong>Months until broke</strong>
                                        <div>
                                            @if (is_null($monthsUntilBroke))
                                                <span class="text-muted">Not projected</span>
                                            @else
                                                <strong>{{ $monthsUntilBroke }}</strong>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="min-width:260px; max-width:360px;">
                                <div class="mb-2">
                                    <label class="form-label mb-1">Savings Goal</label>
                                    @if ($savingsGoal)
                                        @php
                                            $progress =
                                                $currentBalance > 0
                                                    ? min(100, round(($currentBalance / $savingsGoal) * 100))
                                                    : 0;
                                        @endphp
                                        <div class="progress" style="height:18px;">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}"
                                                aria-valuemin="0" aria-valuemax="100">
                                                {{ $progress }}%
                                            </div>
                                        </div>
                                        <div class="mt-2 small text-muted">Goal: ${{ number_format($savingsGoal, 2) }} ·
                                            You:
                                            ${{ number_format($currentBalance, 2) }}</div>
                                    @else
                                        <div class="small text-muted mb-2">No savings goal set.</div>
                                    @endif

                                    <!-- Set Goal button -->
                                    <button class="btn btn-outline-light btn-sm mt-2" data-bs-toggle="modal"
                                        data-bs-target="#setGoalModal">
                                        {{ $savingsGoal ? 'Update Savings Goal' : 'Set Savings Goal' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Set Savings Goal Modal -->
                <div class="modal fade" id="setGoalModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-sm modal-dialog-centered">
                        <div class="modal-content bg-light text-dark">
                            <div class="modal-header">
                                <h5 class="modal-title">Set Savings Goal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form method="POST" action="{{ route('setSavingsGoal') }}">
                                @csrf
                                <div class="modal-body">
                                    <label for="goalInput" class="form-label">Goal amount (CAD)</label>
                                    <input id="goalInput" name="goal" type="number" step="0.01" min="1"
                                        class="form-control" required value="{{ old('goal', $savingsGoal ?? '') }}">
                                    <div class="form-text">We store this in your session (you can change later). Persist to
                                        DB if you want permanent storage.</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Save Goal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Charts and Highest Expense Table -->
                <div class="row">
                    <!-- Monthly Expenses -->
                    <div class="col-md-6 mt-4">
                        <div class="card p-3 shadow-lg rounded-2xl">
                            <h4 class="text-center mb-3">Monthly Expenses</h4>
                            <div class="chart-container">
                                <canvas id="monthlyExpenseChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Montly Income -->
                    <div class="col-md-6 mt-4">
                        <div class="card p-3 shadow-lg rounded-2xl">
                            <h4 class="text-center mb-3">Monthly Income</h4>
                            <div class="chart-container">
                                <canvas id="monthlyIncomeChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- monthly saving -->
                    <div class="col-md-6 mt-4">
                        <div class="card p-3 shadow-lg rounded-2xl">
                            <h4 class="text-center mb-3">Monthly Savings</h4>
                            <div class="chart-container">
                                <canvas id="monthlySavingsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- highest expense -->
                    <div class="col-md-6 mt-4">
                        <div class="card p-3 shadow-lg rounded-2xl">
                            <h4 class="text-center mb-3">Highest Expenses</h4>
                            <div class="chart-container">
                                <table id="highestExpense" class="display">
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
                                        @foreach ($highestExpenses as $expense)
                                            <tr>
                                                <td>{{ $expense->expense }}</td>
                                                <td>{{ $expense->category->category }}</td>
                                                <td>{{ $expense->cost }}</td>
                                                <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    @if ($expense->created_at != $expense->updated_at)
                                                        {{ $expense->updated_at->diffForHumans() }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- caseflow forecast -->
                    <div class="col-md-6 mt-4">
                        <div class="card p-3 shadow-lg rounded-2xl">
                            <h4 class="text-center mb-3">Cashflow Forecast (Next 6 Months)</h4>
                            <div class="chart-container">
                                <canvas id="forecastChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Pie Chart for Category Wise Expenses -->
                    <div class="col-md-6 mt-4">
                        <div class="card p-3 shadow-lg rounded-2xl">
                            <h4 class="text-center mb-3">Expenses by Category</h4>
                            <div class="chart-container">
                                <canvas id="expensesPieChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- Line Chart for Income vs Expenses -->
                    <div class="col-md-12 mt-4">
                        <div class="card p-3 shadow-lg rounded-2xl">
                            <h4 class="text-center mb-3">Monthly Income vs Expenses</h4>
                            <div class="chart-container">
                                <canvas id="incomeExpenseChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expense Tab -->
            <div id="addExpense" class="tab-content">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">Add Expenses</h4>
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
                                <input type="radio" value="0" name="subscription" class="ms-2"> No
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

                {{-- All Added Expenses Table --}}
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="mb-3">All Added Expenses</h4>

                        <div class="row g-2 mb-3">
                            <div class="col-sm-6">
                                <label for="expenseMonthFilter" class="form-label">Filter by Month</label>
                                <input type="month" id="expenseMonthFilter" class="form-control"
                                    placeholder="Show all">
                            </div>
                            <div class="col-sm-6">
                                <label for="expenseYearFilter" class="form-label">Filter by Year</label>
                                <select id="expenseYearFilter" class="form-select">
                                    <option value="">Show all</option>
                                    {{-- options populated by JS from table data --}}
                                </select>
                            </div>
                        </div>

                        <table id="expenseTable" class="display">
                            <thead>
                                <tr>
                                    <th>Expense</th>
                                    <th>Category</th>
                                    <th>Subscription</th>
                                    <th>Cost</th>
                                    <th>Added at</th>
                                    <th>Updated at</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenses as $expense)
                                    <tr e-id="{{ $expense->id }}" e-name="{{ $expense->expense }}"
                                        e-category="{{ $expense->categoryId }}"
                                        e-subscription="{{ $expense->subscription }}" e-cost="{{ $expense->cost }}"
                                        e-description="{{ $expense->description }}">
                                        <td>{{ $expense->expense }}</td>
                                        <td>{{ $expense->category->category }}</td>
                                        <td>{{ $expense->subscription ? 'Yes' : 'No' }}</td>
                                        <td>{{ $expense->cost }}</td>
                                        <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            @if ($expense->created_at != $expense->updated_at)
                                                {{ $expense->updated_at->diffForHumans() }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-around">
                                                <button class="btn btn-primary editExpenseBtn" data-bs-toggle="modal"
                                                    data-bs-target="#editExpenseModal">Edit</button>
                                                <form action="{{ route('deleteExpense') }}" method="POST"
                                                    class="ms-2">
                                                    @csrf
                                                    <input type="hidden" value="{{ $expense->id }}" name="expenseId">
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <!-- Income Tab -->
            <div id="addIncome" class="tab-content">
                <!-- add expenses -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">Add Expenses</h4>
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
                                <input type="radio" value="0" name="mrr" class="ms-2"> No
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

                <!-- All Added Income Table -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">All Added Income Table</h4>
                        <div class="row g-2 mb-3">
                            <div class="col-sm-6">
                                <label for="incomeMonthFilter" class="form-label">Filter by Month</label>
                                <input type="month" id="incomeMonthFilter" class="form-control"
                                    placeholder="Show all">
                            </div>
                            <div class="col-sm-6">
                                <label for="incomeYearFilter" class="form-label">Filter by Year</label>
                                <select id="incomeYearFilter" class="form-select">
                                    <option value="">Show all</option>
                                    {{-- options populated by JS from table data --}}
                                </select>
                            </div>
                        </div>
                        <table id="incomeTable" class="display">
                            <thead>
                                <tr>
                                    <th>Income</th>
                                    <th>Category</th>
                                    <th>Cost</th>
                                    <th>Added at</th>
                                    <th>Updated at</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($incomes as $income)
                                    <tr income-id="{{ $income->id }}" income-name="{{ $income->label }}"
                                        income-category ="{{ $income->category->category }}"
                                        income-revenue = "{{ $income->revenue }}"
                                        income-description="{{ $income->description }}">
                                        <td>{{ $income->label }}</td>
                                        <td>{{ $income->category->category }}</td>
                                        <td>{{ $income->revenue }}</td>
                                        <td>{{ $income->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            @if ($income->created_at != $income->updated_at)
                                                {{ $income->updated_at->diffForHumans() }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-around">
                                                <button class="btn btn-primary editIncomeBtn"
                                                    data-id="{{ $income->id }}" data-label="{{ $income->label }}"
                                                    data-category="{{ $income->categoryId }}"
                                                    data-mrr="{{ $income->mrr }}" data-revenue="{{ $income->revenue }}"
                                                    data-description="{{ $income->description }}" data-bs-toggle="modal"
                                                    data-bs-target="#editIncomeModal">
                                                    Edit
                                                </button>
                                                <form action="{{ route('deleteIncome') }}" method="POST"
                                                    class="ms-2">
                                                    @csrf
                                                    <input type="hidden" value="{{ $income->id }}" name="incomeId">
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <!-- Budget Tab -->
            <div id="budget" class="tab-content">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">Set Monthly Budgets</h4>
                        <form method="POST" action="{{ route('budgets.store') }}">
                            @csrf
                            <div class="row">
                                @foreach ($categories as $category)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ $category->category }}</label>
                                        @error('budgets.' . $category->id)
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror
                                        <input type="number" step="0.01" min="0"
                                            name="budgets[{{ $category->id }}]" class="form-control"
                                            placeholder="Set budget for {{ $category->category }}"
                                            value="{{ old('budgets.' . $category->id, $category->budget ?? '') }}">
                                    </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-success">Save Budgets</button>
                        </form>
                    </div>
                </div>

                <!-- Budget Overview -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h4 class="mb-3">Budget Overview</h4>
                        <div class="row">
                            @foreach ($categories as $category)
                                @php
                                    $spent = (float) ($spentByCategoryThisMonth[$category->id] ?? 0);
                                    $budget = (float) ($category->budget ?? 0);
                                    $progress = $budget > 0 ? min(100, round(($spent / $budget) * 100)) : 0;
                                    $barColor =
                                        $progress < 75 ? 'bg-success' : ($progress < 100 ? 'bg-warning' : 'bg-danger');
                                @endphp

                                <div class="col-md-6 mb-3">
                                    <h6>{{ $category->category }}</h6>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar {{ $barColor }}" role="progressbar"
                                            style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}"
                                            aria-valuemin="0" aria-valuemax="100">
                                            {{ $progress }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        Spent: ${{ number_format($spent, 2) }} /
                                        Budget: ${{ number_format($budget, 2) }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Tab -->
            <div id="category" class="tab-content">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">Add Category</h4>
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
                    <div class="card-body">
                        <h4 class="mb-3"> All Categories Added</h4>
                        <table id="categoryTable" class="display">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Added at</th>
                                    <th>Updated at</th>
                                    <th>Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr cat-id="{{ $category->id }}" cat-name="{{ $category->category }}"
                                        cat-description="{{ $category->description }}">
                                        <td>{{ $category->category }}</td>
                                        <td>
                                            {{ $category->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            @if ($category->created_at != $category->updated_at)
                                                {{ $category->updated_at->diffForHumans() }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex flex-row justify-content-around">
                                                <div>
                                                    <!-- edit Button trigger -->
                                                    <button class="btn btn-primary editCategoryBtn" data-bs-toggle="modal"
                                                        data-bs-target="#editCategoryModal">Edit</button>
                                                </div>
                                                <div class="ml-3">
                                                    <!-- delete button -->
                                                    <form action="{{ route('deleteCategory') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" value="{{ $category->id }}"
                                                            name="categoryId">
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Edit Category Modal -->
            <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if ($errors->any() && session('editCategoryId'))
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form id="editCategoryForm" method="POST" action="{{ route('editCategory') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="categoryName" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="categoryName" name="categoryName"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="categoryDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="categoryDescription" name="cdescription"></textarea>
                                </div>
                                <input type="hidden" class="form-control" id="categoryId" name="categoryId" required>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <!-- FIX: button submits the form -->
                            <button type="submit" class="btn btn-primary" form="editCategoryForm">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Income Modal -->
            <div class="modal fade" id="editIncomeModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Income Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if ($errors->any() && session('editIncomeId'))
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form id="editIncomeForm" method="POST" action="{{ route('editIncome') }}">
                                @csrf
                                <input type="hidden" id="incomeId" name="incomeId">

                                <div class="mb-2">
                                    <label class="form-label">Income</label>
                                    <input type="text" id="editLabel" name="label" placeholder="Income Label"
                                        class="form-control" required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Category</label>
                                    <select id="editCategoryId" name="icategoryId" class="form-control" required>
                                        <option disabled>-- Category --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->category }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Is it a Monthly Recurring Revenue (MRR):</label>
                                    <input type="radio" id="editMrrYes" value="1" name="mrr"> Yes
                                    <input type="radio" id="editMrrNo" value="0" name="mrr"> No
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Revenue</label>
                                    <input type="number" id="editRevenue" name="revenue" placeholder="Revenue"
                                        class="form-control" step="any" required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Description</label>
                                    <textarea id="editDescription" name="idescription" cols="30" rows="5" class="form-control"></textarea>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" form="editIncomeForm" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Expense Modal -->
            <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Expense</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if ($errors->any() && session('editExpenseId'))
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form id="editExpenseForm" method="POST" action="{{ route('editExpense') }}">
                                @csrf
                                <input type="hidden" id="expenseId" name="expenseId" required>

                                <div class="mb-3">
                                    <label for="expenseName" class="form-label">Expense Name</label>
                                    <input type="text" class="form-control" id="expenseName" name="expense" required>
                                </div>

                                <div class="mb-3">
                                    <label for="expenseCategory" class="form-label">Category</label>
                                    <select id="expenseCategory" name="ecategoryId" class="form-control" required>
                                        <option disabled>-- Category --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->category }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Subscription:</label><br>
                                    <input type="radio" id="subYes" value="1" name="subscription"> Yes
                                    <input type="radio" id="subNo" value="0" name="subscription"> No
                                </div>

                                <div class="mb-3">
                                    <label for="expenseCost" class="form-label">Cost</label>
                                    <input type="number" step="any" id="expenseCost" name="cost"
                                        class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="expenseDescription" class="form-label">Description</label>
                                    <textarea id="expenseDescription" name="edescription" rows="4" class="form-control"></textarea>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" form="editExpenseForm" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- /.tab-container -->
    </main>
@endsection

@section('customJavascript')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            /* ===========================
               DataTables
            ============================ */
            if (window.jQuery && $.fn.DataTable) {
                $('#expenseTable').DataTable();
                $('#incomeTable').DataTable();
                $('#categoryTable').DataTable();
                $('#highestExpense').DataTable({
                    order: [
                        [2, 'desc']
                    ],
                    searching: false, // hide search box
                    lengthChange: false // hide "Show N entries"
                });
                if ($('#budgetTable').length) {
                    $('#budgetTable').DataTable({
                        responsive: true,
                        pageLength: 10,
                        lengthChange: false,
                        autoWidth: false
                    });
                }
            }

            /* ===========================
               Tabs
            ============================ */
            const tabLinks = document.querySelectorAll(".tab-link");
            const tabContents = document.querySelectorAll(".tab-content");
            let overviewRendered = false;

            tabLinks.forEach(link => {
                link.addEventListener("click", () => {
                    tabLinks.forEach(l => l.classList.remove("active"));
                    tabContents.forEach(c => c.classList.remove("active"));
                    link.classList.add("active");
                    document.getElementById(link.dataset.target).classList.add("active");
                    if (link.dataset.target === 'overview' && !overviewRendered) {
                        renderOverviewCharts();
                        overviewRendered = true;
                    }
                });
            });

            const activeTab = "{{ session('activeTab', 'overview') }}";
            tabLinks.forEach(l => l.classList.toggle('active', l.dataset.target === activeTab));
            tabContents.forEach(c => c.classList.toggle('active', c.id === activeTab));

            /* ===========================
               Month filter defaults
            ============================ */
            // Populate Year <select> options by scanning table date column
            function populateYearsFromTable(tableId, dateColIndex, selectId) {
                const years = new Set();
                const $rows = $(`#${tableId} tbody tr`);
                $rows.each(function() {
                    const txt = ($(this).find('td').eq(dateColIndex).text() || '').trim();
                    // expect YYYY-MM-DD
                    const y = txt.slice(0, 4);
                    if (/^\d{4}$/.test(y)) years.add(y);
                });

                const sel = document.getElementById(selectId);
                if (!sel) return;
                const sorted = Array.from(years).sort((a, b) => b.localeCompare(a)); // desc
                sel.innerHTML = '<option value="">Show all</option>' +
                    sorted.map(y => `<option value="${y}">${y}</option>`).join('');
            }

            // Apply combined filters: month (takes precedence) or year; both blank => show all
            function applyDateFilters(tableId, dateColIndex, monthValue, yearValue) {
                if (!window.jQuery || !$.fn.dataTable) return;
                const dt = $(`#${tableId}`).DataTable();

                $.fn.dataTable.ext.search.push((settings, data) => {
                    if (settings.nTable.id !== tableId) return true;

                    const cell = data[dateColIndex];
                    const d = new Date(cell);
                    if (isNaN(d)) return false;

                    if (monthValue) {
                        const y = parseInt(monthValue.substring(0, 4), 10);
                        const m = parseInt(monthValue.substring(5, 7), 10);
                        return d.getFullYear() === y && (d.getMonth() + 1) === m;
                    }
                    if (yearValue) {
                        return d.getFullYear() === parseInt(yearValue, 10);
                    }
                    return true; // show all
                });

                dt.draw();
                $.fn.dataTable.ext.search.pop();
            }

            // Hook up both filters for a table
            function initMonthYearFilters({
                tableId,
                dateColIndex,
                monthInputId,
                yearSelectId
            }) {
                // Leave month blank (default "show all")
                const monthEl = document.getElementById(monthInputId);
                const yearEl = document.getElementById(yearSelectId);
                if (monthEl) monthEl.value = '';
                if (yearEl) yearEl.value = '';

                // Populate year dropdown from table content
                populateYearsFromTable(tableId, dateColIndex, yearSelectId);

                const handler = () => {
                    const monthVal = monthEl?.value || '';
                    const yearVal = yearEl?.value || '';
                    applyDateFilters(tableId, dateColIndex, monthVal, yearVal);
                };

                monthEl?.addEventListener('change', handler);
                yearEl?.addEventListener('change', handler);
            }

            // Expenses: date column is index 4 ("Added at")
            initMonthYearFilters({
                tableId: 'expenseTable',
                dateColIndex: 4,
                monthInputId: 'expenseMonthFilter',
                yearSelectId: 'expenseYearFilter',
            });

            // Income: date column is index 3 ("Added at")
            initMonthYearFilters({
                tableId: 'incomeTable',
                dateColIndex: 3,
                monthInputId: 'incomeMonthFilter',
                yearSelectId: 'incomeYearFilter',
            });

            /* ===========================
               Edit modals (Category/Expense/Income)
            ============================ */
            const setupEditModal = (btnSelector, formFields, rowAttrPrefix) => {
                document.querySelectorAll(btnSelector).forEach(btn => {
                    btn.addEventListener('click', function() {
                        const row = this.closest('tr');
                        for (const [fieldId, attrName] of Object.entries(formFields)) {
                            const el = document.getElementById(fieldId);
                            if (el && row) el.value = row.getAttribute(
                                `${rowAttrPrefix}-${attrName}`) ?? '';
                        }
                    });
                });
            };
            setupEditModal('.editCategoryBtn', {
                categoryName: 'name',
                categoryDescription: 'description',
                categoryId: 'id'
            }, 'cat');
            setupEditModal('.editExpenseBtn', {
                expenseId: 'id',
                expenseName: 'name',
                expenseCategory: 'category',
                expenseCost: 'cost',
                expenseDescription: 'description'
            }, 'e');

            document.querySelectorAll('.editIncomeBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const ds = this.dataset;
                    const idEl = document.getElementById('incomeId');
                    const label = document.getElementById('editLabel');
                    const catEl = document.getElementById('editCategoryId');
                    const revEl = document.getElementById('editRevenue');
                    const descEl = document.getElementById('editDescription');
                    const mrrYes = document.getElementById('editMrrYes');
                    const mrrNo = document.getElementById('editMrrNo');
                    if (idEl) idEl.value = ds.id ?? '';
                    if (label) label.value = ds.label ?? '';
                    if (catEl) catEl.value = ds.category ?? '';
                    if (revEl) revEl.value = ds.revenue ?? '';
                    if (descEl) descEl.value = ds.description ?? '';
                    if (typeof ds.mrr !== 'undefined') {
                        if (String(ds.mrr) === '1') {
                            mrrYes.checked = true;
                            mrrNo.checked = false;
                        } else {
                            mrrYes.checked = false;
                            mrrNo.checked = true;
                        }
                    }
                });
            });

            /* ===========================
               Chart data from PHP
               (subscription-aware pie data is prepared in the controller)
            ============================ */
            const monthsShort = @json($months);
            const monthlyDatas = @json($monthlyDatas);
            const monthlyIncome = @json($monthlyIncome);
            const monthlyExpenses = @json($monthlyExpenses);

            const pieLabels = @json($pieLabels);
            const pieData = @json($pieData);
            const pieIsCurrentMonth = @json($pieIsCurrentMonth);

            /* ===========================
               Chart.js defaults & helpers
            ============================ */
            if (window.Chart) {
                Chart.defaults.color = '#ffffff';
                Chart.defaults.font.size = 12;
                Chart.defaults.plugins.legend.labels.boxWidth = 14;
                Chart.defaults.plugins.legend.labels.boxHeight = 14;
                Chart.defaults.elements.point.radius = 3;
                Chart.defaults.elements.point.hoverRadius = 6;
                Chart.defaults.animation.duration = 900;
                Chart.defaults.animations.colors = {
                    type: 'color',
                    duration: 700
                };
                Chart.defaults.animations.numbers = {
                    type: 'number',
                    duration: 700
                };
                Chart.defaults.transitions.active.animation.duration = 200;
            }

            const $id = (x) => document.getElementById(x);
            window.__charts = window.__charts || {};

            const safeYScale = {
                beginAtZero: true,
                grid: {
                    color: 'rgba(255,255,255,0.15)'
                },
                ticks: {
                    maxTicksLimit: 8,
                    callback: (val) => {
                        try {
                            return Number(val).toLocaleString();
                        } catch {
                            return val;
                        }
                    }
                }
            };
            const safeXGrid = {
                grid: {
                    color: 'rgba(255,255,255,0.15)'
                }
            };

            // gradient helper
            function makeGradient(ctx, area, from = 'rgba(255,255,255,0.20)', to = 'rgba(255,255,255,0.02)') {
                const gradient = ctx.createLinearGradient(0, area.bottom, 0, area.top);
                gradient.addColorStop(0, from);
                gradient.addColorStop(1, to);
                return gradient;
            }

            // keep canvas height pinned to container for stable animations
            function pinCanvasHeight(canvas) {
                const parent = canvas?.parentElement;
                if (!parent) return;
                const h = parent.clientHeight || 340;
                canvas.style.height = h + 'px';
                canvas.height = h;
            }

            function makeOrUpdateChart(key, canvasId, configBuilder) {
                const canvas = $id(canvasId);
                if (!canvas) return;
                pinCanvasHeight(canvas);

                const existing = window.__charts[key];
                const cfg = configBuilder();

                if (existing) {
                    existing.config.type = cfg.type || existing.config.type;
                    existing.data.labels = cfg.data.labels;
                    existing.data.datasets = cfg.data.datasets;
                    existing.options = Object.assign(existing.options, cfg.options || {});
                    existing.update();
                    return existing;
                } else {
                    const chart = new Chart(canvas.getContext('2d'), {
                        type: cfg.type,
                        data: cfg.data,
                        options: Object.assign({
                            responsive: true,
                            maintainAspectRatio: false,
                            resizeDelay: 100
                        }, cfg.options || {})
                    });
                    window.__charts[key] = chart;
                    return chart;
                }
            }

            /* ===========================
               Charts (render on Overview)
            ============================ */
            function renderOverviewCharts() {
                const labelsFromMonthly = monthlyDatas.map(d => monthsShort[(d.month ?? 0) - 1] ?? '');

                // 1) Monthly Expenses (doughnut)
                makeOrUpdateChart('monthlyExpenseChart', 'monthlyExpenseChart', () => ({
                    type: 'doughnut',
                    data: {
                        labels: labelsFromMonthly,
                        datasets: [{
                            label: 'Total Spendings',
                            data: monthlyDatas.map(d => d.expense),
                            backgroundColor: ['#FF6B6B', '#4FC3F7', '#FFD166', '#6EE7B7',
                                '#A78BFA', '#26C6DA', '#FCA5A5', '#F59E0B'
                            ],
                            borderColor: '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        animation: {
                            animateRotate: true,
                            animateScale: true
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                }));

                // 2) Monthly Income (bar) — gradient aligned with page background (green → blue)
                makeOrUpdateChart('monthlyIncomeChart', 'monthlyIncomeChart', () => ({
                    type: 'bar',
                    data: {
                        labels: labelsFromMonthly,
                        datasets: [{
                            label: 'Total Revenue',
                            data: monthlyDatas.map(d => d.income),
                            // Scriptable background so it always matches current size
                            backgroundColor: (ctx) => {
                                const chart = ctx.chart;
                                const {
                                    ctx: c,
                                    chartArea
                                } = chart;
                                if (!chartArea) return '#74b9ff';
                                const grad = c.createLinearGradient(chartArea.left, 0,
                                    chartArea.right, 0);
                                grad.addColorStop(0, '#28c76f'); // brand green
                                grad.addColorStop(1, '#0099ff'); // brand blue
                                return grad;
                            },
                            borderColor: 'rgba(255,255,255,0.65)',
                            borderWidth: 1,
                            hoverBackgroundColor: (ctx) => {
                                const chart = ctx.chart;
                                const {
                                    ctx: c,
                                    chartArea
                                } = chart;
                                if (!chartArea) return '#74b9ff';
                                const grad = c.createLinearGradient(chartArea.left, 0,
                                    chartArea.right, 0);
                                grad.addColorStop(0, 'rgba(40,199,111,0.95)');
                                grad.addColorStop(1, 'rgba(0,153,255,0.95)');
                                return grad;
                            }
                        }]
                    },
                    options: {
                        scales: {
                            x: safeXGrid,
                            y: safeYScale
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                }));

                // 3) Monthly Savings (line + gradient)
                makeOrUpdateChart('monthlySavingsChart', 'monthlySavingsChart', () => {
                    const canvas = $id('monthlySavingsChart');
                    const ctx = canvas.getContext('2d');
                    const area = {
                        top: 0,
                        bottom: canvas.height
                    };
                    const bg = makeGradient(ctx, area, 'rgba(255,255,255,0.25)', 'rgba(255,255,255,0.04)');
                    return {
                        type: 'line',
                        data: {
                            labels: labelsFromMonthly,
                            datasets: [{
                                label: 'Total Savings',
                                data: monthlyDatas.map(d => d.savings),
                                borderColor: '#FFFFFF',
                                backgroundColor: bg,
                                fill: true,
                                tension: 0.35
                            }]
                        },
                        options: {
                            scales: {
                                x: safeXGrid,
                                y: safeYScale
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    };
                });

                // 4) Expenses by Category (PIE) — subscription-aware
                makeOrUpdateChart('expensesPieChart', 'expensesPieChart', () => ({
                    type: 'pie',
                    data: {
                        labels: pieLabels,
                        datasets: [{
                            data: pieData,
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                                '#9966FF', '#FF9F40', '#8DD3C7', '#BC80BD', '#80B1D3',
                                '#FB8072'
                            ],
                            borderColor: '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        animation: {
                            animateRotate: true,
                            animateScale: true
                        },
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    title: (items) => {
                                        const scope = pieIsCurrentMonth ? 'This month' : 'All time';
                                        return `${items[0].label} — ${scope}`;
                                    },
                                    label: (ctx) => `${Number(ctx.parsed || 0).toLocaleString()}`
                                }
                            }
                        }
                    }
                }));

                // 5) Income vs Expenses (12-month lines + gradient)
                makeOrUpdateChart('incomeExpenseChart', 'incomeExpenseChart', () => {
                    const canvas = $id('incomeExpenseChart');
                    const ctx = canvas.getContext('2d');
                    const area = {
                        top: 0,
                        bottom: canvas.height
                    };
                    const gradIncome = makeGradient(ctx, area, 'rgba(0,230,118,0.25)',
                        'rgba(0,230,118,0.02)');
                    const gradExpense = makeGradient(ctx, area, 'rgba(255,82,82,0.25)',
                        'rgba(255,82,82,0.02)');
                    return {
                        type: 'line',
                        data: {
                            labels: monthsShort,
                            datasets: [{
                                    label: 'Income',
                                    data: monthlyIncome,
                                    borderColor: '#00E676',
                                    backgroundColor: gradIncome,
                                    fill: true,
                                    tension: 0.35
                                },
                                {
                                    label: 'Expenses',
                                    data: monthlyExpenses,
                                    borderColor: '#FF5252',
                                    backgroundColor: gradExpense,
                                    fill: true,
                                    tension: 0.35
                                }
                            ]
                        },
                        options: {
                            scales: {
                                x: safeXGrid,
                                y: safeYScale
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    };
                });

                // 6) Forecast (AJAX once)
                if (window.jQuery && !window.__forecastLoaded) {
                    window.__forecastLoaded = true;
                    $.get("{{ route('forecast.data') }}")
                        .done((data) => {
                            makeOrUpdateChart('forecastChart', 'forecastChart', () => ({
                                type: 'line',
                                data: {
                                    labels: data.months,
                                    datasets: [{
                                        label: 'Projected Balance',
                                        data: data.forecast,
                                        borderColor: '#FFFFFF',
                                        backgroundColor: 'rgba(255,255,255,0.12)',
                                        fill: true,
                                        tension: 0.35
                                    }]
                                },
                                options: {
                                    animation: {
                                        duration: 800
                                    },
                                    scales: {
                                        x: safeXGrid,
                                        y: safeYScale
                                    },
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top'
                                        }
                                    }
                                }
                            }));
                        })
                        .fail(() => {
                            const ctx = $id('forecastChart')?.getContext('2d');
                            if (ctx) {
                                ctx.font = '14px sans-serif';
                                ctx.fillStyle = '#fff';
                                ctx.fillText('Unable to load forecast data.', 10, 20);
                            }
                        });
                }
            }

            // Initial render if Overview tab is showing
            if (document.getElementById('overview')?.classList.contains('active')) {
                renderOverviewCharts();
                overviewRendered = true;
            }
        });
    </script>
@endsection
