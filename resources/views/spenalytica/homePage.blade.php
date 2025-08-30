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
                                                    'Critical' => 'dark',
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
                    <div class="col-md-6">
                        <div class="chart-container">
                            <canvas id="monthlyExpenseChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-container">
                            <canvas id="monthlyIncomeChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-container">
                            <canvas id="monthlySavingsChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
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
                    <div class="col-md-6 mt-4">
                        <div class="card p-3 shadow-lg rounded-2xl">
                            <h4 class="text-center">Cashflow Forecast (Next 6 Months)</h4>
                            <canvas id="forecastChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6 mt-4">
                        <div class="card">
                            <div class="card-body">
                                <!-- Pie Chart for Category Wise Expenses -->
                                <h6>Expenses by Category</h6>
                                <canvas id="expensesPieChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 ">
                        <div class="card">
                            <div class="card-body">
                                <!-- Line Chart for Income vs Expenses -->
                                    <h6>Monthly Income vs Expenses</h6>
                                    <canvas id="incomeExpenseChart"></canvas>
                                </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expense Tab -->
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
                    <div class="card-header">
                        All Expenses Added
                        <div class="mb-3">
                            <label for="expenseMonthFilter">Filter Expenses by Month:</label>
                            <input type="month" id="expenseMonthFilter" name="expenseMonthFilter"
                                class="form-control">
                        </div>
                    </div>
                    <div class="card-body">
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
                                        <td>
                                            @if ($expense->subscription)
                                                Yes
                                            @else
                                                No
                                            @endif
                                        </td>
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
                                            <div class="d-flex flex-row justify-content-around">
                                                <div>
                                                    <!-- edit Button trigger -->
                                                    <button class="btn btn-primary editExpenseBtn" data-bs-toggle="modal"
                                                        data-bs-target="#editExpenseModal">Edit</button>
                                                </div>
                                                <div>
                                                    <!-- delete button -->
                                                    <form action="{{ route('deleteExpense') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" value="{{ $expense->id }}"
                                                            name="expenseId">
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

            <!-- Income Tab -->
            <div id="addIncome" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        Add Income
                    </div>
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
                    <div class="card-header">
                        All Income Revenues Added
                        <div class="mb-3">
                            <label for="incomeMonthFilter">Filter Income by Month:</label>
                            <input type="month" id="incomeMonthFilter" name="incomeMonthFilter" class="form-control">
                        </div>
                    </div>
                    <div class="card-body">
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
                                            <div class="d-flex flex-row justify-content-around">
                                                <div>
                                                    <!-- edit Button trigger -->
                                                    <button class="btn btn-primary editIncomeBtn"
                                                        data-id="{{ $income->id }}" data-label="{{ $income->label }}"
                                                        data-category="{{ $income->categoryId }}"
                                                        data-mrr="{{ $income->mrr }}"
                                                        data-revenue="{{ $income->revenue }}"
                                                        data-description="{{ $income->description }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editIncomeModal">Edit</button>
                                                </div>
                                                <div>
                                                    <!-- delete button -->
                                                    <form action="{{ route('deleteIncome') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" value="{{ $income->id }}"
                                                            name="incomeId">
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

            <!-- Budget Tab -->
            <div id="budget" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        Set Monthly Budgets
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('budgets.store') }}">
                            @csrf
                            <div class="row">
                                @foreach ($categories as $category)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ $category->category }}</label>
                                        <input type="number" name="budgets[{{ $category->id }}]" class="form-control"
                                            placeholder="Set budget for {{ $category->category }}"
                                            value="{{ old('budgets.' . $category->id, $category->budget ?? 0) }}">
                                    </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-success">Save Budgets</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        Budget Overview
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($categories as $category)
                                @php
                                    // Calculate spent this month
                                    $spent = $expenses
                                        ->where('categoryId', $category->id)
                                        ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                                        ->sum('cost');
                                    $budget = $category->budget ?? 0;
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

            {{-- <!-- Tab Navigation (example) -->
            <div class="tab-nav">
                <button class="tab-link {{ $activeTab == 'overview' ? 'active' : '' }}"
                    data-tab="overview">Overview</button>
                <button class="tab-link {{ $activeTab == 'budget' ? 'active' : '' }}" data-tab="budget">Budget</button>
            </div> --}}

            <!-- Category Tab -->
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
            <div class="modal fade" id="editCategoryModal" tabindex="-9" aria-hidden="true">
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
                            <button type="submit" class="btn btn-primary">Save changes</button>
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



    </main>
@endsection

@section('customJavascript')
    @php
        $expenseData = $categories->map(function ($cat) use ($expenses) {
            return $expenses
                ->where('categoryId', $cat->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('cost');
        });
    @endphp
    <script>
        //data table
        $(document).ready(function() {
            $('#expenseTable').DataTable();
            $('#incomeTable').DataTable();
            $('#categoryTable').DataTable();
            $('#highestExpense').DataTable({
                order: [
                    [2, 'desc']
                ]
            });
        });

        //Tab JS
        document.querySelectorAll('.tab-link').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const tab = this.getAttribute('data-tab');
                document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
                document.querySelector('#' + tab).classList.add('active');

                document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        //default datatable value 
        document.addEventListener("DOMContentLoaded", () => {

            //tab link javascript
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

            //when validation get success or failed , stay on the same tab 
            let activeTab = "{{ session('activeTab', 'overview') }}"; // default to overview
            document.querySelectorAll(".tab-link").forEach(link => {
                link.classList.remove("active");
                if (link.getAttribute("data-target") === activeTab) {
                    link.classList.add("active");
                }
            });
            document.querySelectorAll(".tab-content").forEach(content => {
                content.classList.remove("active");
                if (content.id === activeTab) {
                    content.classList.add("active");
                }
            });

            // Set current month as default value YYYY-MM
            function setCurrentMonthInput(id) {
                const input = document.getElementById(id);
                if (input) {
                    const now = new Date();
                    const month = now.getMonth() + 1; // getMonth() is 0-based
                    const monthString = month < 10 ? `0${month}` : month;
                    const defaultValue = `${now.getFullYear()}-${monthString}`;
                    input.value = defaultValue;
                }
            }

            setCurrentMonthInput('expenseMonthFilter');
            setCurrentMonthInput('incomeMonthFilter');

            // TODO: Add your filtering logic here
            // For example, filter the DataTables based on selected month
            // You might want to listen to 'change' event on these inputs to update tables dynamically

            //once the date is selected 
            function filterByMonth(tableId, dateColumnIndex, monthValue) {
                const year = monthValue.substring(0, 4);
                const month = monthValue.substring(5, 7);

                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    if (settings.nTable.id !== tableId) return true;

                    const dateStr = data[dateColumnIndex]; // Use the passed parameter here
                    const date = new Date(dateStr);

                    return date.getFullYear() == year && (date.getMonth() + 1) == parseInt(month);
                });

                $(`#${tableId}`).DataTable().draw();

                $.fn.dataTable.ext.search.pop();
            }

            document.getElementById('expenseMonthFilter').addEventListener('change', function() {
                filterByMonth('expenseTable', 4, this.value);
            });

            document.getElementById('incomeMonthFilter').addEventListener('change', function() {
                filterByMonth('incomeTable', 3, this.value);
            });

            //edit Category model 
            document.querySelectorAll('.editCategoryBtn').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const categoryId = row.getAttribute('cat-id');
                    const categoryName = row.getAttribute('cat-name');
                    const categoryDescription = row.getAttribute('cat-description');
                    document.getElementById('categoryName').value = categoryName;
                    document.getElementById('categoryDescription').value = categoryDescription;
                    document.getElementById('categoryId').value = categoryId;
                });
            });

            //validation error for set goals
            @if ($errors->any() && session('activeModal') == 'setGoal')
                var modal = new bootstrap.Modal(document.getElementById('setGoalModal'));
                modal.show();
            @endif

            //when there is validation error at category modal
            @if ($errors->any() && session('editCategoryId'))
                var editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                editModal.show();

                // Also set the form action and inputs values again
                const row = document.querySelector(`tr[cat-id="{{ session('editCategoryId') }}"]`);
                if (row) {
                    document.getElementById('categoryName').value = row.getAttribute('cat-name');
                    document.getElementById('categoryDescription').value = row.getAttribute('cat-description');
                }
            @endif
            //edit Income model
            const editButtons = document.querySelectorAll('.editIncomeBtn');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Get data attributes from the clicked button
                    const id = this.getAttribute('data-id');
                    const label = this.getAttribute('data-label');
                    const category = this.getAttribute('data-category');
                    const mrr = this.getAttribute('data-mrr');
                    const revenue = this.getAttribute('data-revenue');
                    const description = this.getAttribute('data-description');

                    // Fill modal fields
                    document.getElementById('incomeId').value = id;
                    document.getElementById('editLabel').value = label;
                    document.getElementById('editCategoryId').value = category;
                    document.getElementById(mrr === "1" ? 'editMrrYes' : 'editMrrNo')
                        .checked = true;
                    document.getElementById('editRevenue').value = revenue;
                    document.getElementById('editDescription').value = description;
                });
            });
            //When there is validation error at income modal
            @if ($errors->any() && session('editIncomeId'))
                var editIncomeModal = new bootstrap.Modal(document.getElementById('editIncomeModal'));
                editIncomeModal.show();

                // Set the form fields again based on the table row attributes
                const incomeRow = document.querySelector(`tr[income-id="{{ session('editIncomeId') }}"]`);
                if (incomeRow) {
                    document.getElementById('incomeName').value = incomeRow.getAttribute('income-name');
                    document.getElementById('incomeCategory').value = incomeRow.getAttribute('income-category');
                    document.getElementById('incomeRevenue').value = incomeRow.getAttribute('income-revenue');
                    document.getElementById('incomeDescription').value = incomeRow.getAttribute(
                        'income-description');
                }
            @endif

            // When clicking "Edit" on Expense
            document.querySelectorAll('.editExpenseBtn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');

                    document.getElementById('expenseId').value = row.getAttribute('e-id');
                    document.getElementById('expenseName').value = row.getAttribute('e-name');
                    document.getElementById('expenseCategory').value = row.getAttribute(
                        'e-category');
                    document.getElementById('expenseCost').value = row.getAttribute('e-cost');
                    document.getElementById('expenseDescription').value = row.getAttribute(
                        'e-description');

                    // Set subscription radio
                    const subValue = row.getAttribute('e-subscription');
                    document.getElementById('subYes').checked = subValue === "1";
                    document.getElementById('subNo').checked = subValue === "0";
                });
            });

            // When validation error occurs for edit expense
            @if ($errors->any() && session('editExpenseId'))
                var editExpenseModal = new bootstrap.Modal(document.getElementById('editExpenseModal'));
                editExpenseModal.show();

                const expenseRow = document.querySelector(`tr[e-id="{{ session('editExpenseId') }}"]`);
                if (expenseRow) {
                    document.getElementById('expenseId').value = expenseRow.getAttribute('e-id');
                    document.getElementById('expenseName').value = expenseRow.getAttribute('e-name');
                    document.getElementById('expenseCategory').value = expenseRow.getAttribute('e-category');
                    document.getElementById('expenseCost').value = expenseRow.getAttribute('e-cost');
                    document.getElementById('expenseDescription').value = expenseRow.getAttribute('e-description');

                    const subValue = expenseRow.getAttribute('e-subscription');
                    document.getElementById('subYes').checked = subValue === "1";
                    document.getElementById('subNo').checked = subValue === "0";
                }
            @endif


            // DataTable init for budgets if table exists
            if (document.getElementById('budgetTable')) {
                $('#budgetTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthChange: false,
                    autoWidth: false
                });
            }

            // Edit budget button handler
            document.querySelectorAll('.editBudgetBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tr = this.closest('tr');
                    const id = tr.dataset.id;
                    const categoryId = tr.dataset.category || '';
                    const amount = tr.dataset.amount || '';
                    const period = tr.dataset.period || 'monthly';
                    const start = tr.dataset.start || '';
                    const active = tr.dataset.active === '1' || tr.dataset.active === 'true';

                    // set values
                    document.getElementById('editAmountInput').value = amount;
                    document.getElementById('editStartDate').value = start;
                    document.getElementById('editPeriodSelect').value = period;
                    document.getElementById('editCategorySelect').value = categoryId;
                    document.getElementById('editActive').checked = active;
                    document.getElementById('editBudgetId').value = id;

                    // set form action
                    const form = document.getElementById('editBudgetForm');
                    form.action = `/budgets/${id}`;
                });
            });

            // When edit modal submitted, ensure the checkbox value is passed (because unchecked checkbox isn't sent).
            document.getElementById('editBudgetForm')?.addEventListener('submit', function(e) {
                // create a hidden input for active if checked, or set 0
                let activeInput = this.querySelector('input[name="active"][type="hidden"]');
                if (!activeInput) {
                    activeInput = document.createElement('input');
                    activeInput.type = 'hidden';
                    activeInput.name = 'active';
                    this.appendChild(activeInput);
                }
                activeInput.value = document.getElementById('editActive').checked ? 1 : 0;
            });


            //Chart JS
            // Convert Laravel data to JS
            var months = ["", "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];
            const expenses = @json($expenses);
            const monthlyDatas = @json($monthlyDatas);
            const categories = @json($categories);
            // Format expenses by category for chart
            const expenseList = expenses.map(exp => ({
                expense: exp.expense,
                cost: parseFloat(exp.cost)
            }));
            //doughnut chart
            function createDoughnutChart(canvasId, data, label) {
                const ctx = document.getElementById(canvasId).getContext('2d');
                return new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: monthlyDatas.map(e => months[e.month]),
                        datasets: [{
                            label: "Total Spendings",
                            data: monthlyDatas.map(e => e.expense),
                            backgroundColor: [
                                '#4cafef', '#ff9800', '#e91e63', '#8bc34a',
                                '#3f51b5', '#009688', '#f44336', '#9c27b0'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#333',
                                    font: {
                                        size: 14
                                    }
                                }
                            },
                            tooltip: {
                                enabled: true
                            }
                        }
                    }
                });
            }

            //bar chart
            function createBarChart(canvasId, data, label) {
                const bctx = document.getElementById(canvasId).getContext('2d');
                return new Chart(bctx, {
                    type: 'bar',
                    data: {
                        labels: monthlyDatas.map(e => months[e.month]),
                        datasets: [{
                            label: 'Total Revenue',
                            data: monthlyDatas.map(e => e.income),
                            backgroundColor: [
                                '#4cafef', '#ff9800', '#e91e63', '#8bc34a',
                                '#3f51b5', '#009688', '#f44336', '#9c27b0'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#333',
                                    font: {
                                        size: 14
                                    }
                                }
                            },
                            tooltip: {
                                enabled: true
                            }
                        }
                    }
                });
            }

            //line chart
            function createLineChart(canvasId, data, label) {
                const lctx = document.getElementById(canvasId).getContext('2d');
                return new Chart(lctx, {
                    type: 'line',
                    data: {
                        labels: monthlyDatas.map(e => months[e.month]),
                        datasets: [{
                            label: 'Total Savings',
                            data: monthlyDatas.map(e => e.savings),
                            backgroundColor: [
                                '#4cafef', '#ff9800', '#e91e63', '#8bc34a',
                                '#3f51b5', '#009688', '#f44336', '#9c27b0'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#333',
                                    font: {
                                        size: 14
                                    }
                                }
                            },
                            tooltip: {
                                enabled: true
                            }
                        }
                    }
                });
            }

            //line chart 2
            $.get("{{ route('forecast.data') }}", function(data) {
                new Chart(document.getElementById('forecastChart'), {
                    type: 'line',
                    data: {
                        labels: data.months,
                        datasets: [{
                            label: 'Projected Balance',
                            data: data.forecast,
                            borderColor: '#4BC0C0',
                            backgroundColor: 'rgba(75,192,192,0.2)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            },
                            tooltip: {
                                enabled: true
                            }
                        }
                    }
                });
            });

            // Pie Chart: Expenses by Category
            const ctxPie = document.getElementById('expensesPieChart');
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: @json($categories->pluck('category')),
                    datasets: [{
                        label: 'Expenses',
                        data: @json($expenseData),
                        backgroundColor: [
                            '#ff6384', '#36a2eb', '#ffce56',
                            '#4bc0c0', '#9966ff', '#ff9f40'
                        ]
                    }]
                }
            });


            // Line Chart: Income vs Expenses (by month)
            const ctxLine = document.getElementById('incomeExpenseChart');
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: @json($months), // pass from controller
                    datasets: [{
                            label: 'Income',
                            data: @json($monthlyIncome),
                            borderColor: 'green',
                            fill: false
                        },
                        {
                            label: 'Expenses',
                            data: @json($monthlyExpenses),
                            borderColor: 'red',
                            fill: false
                        }
                    ]
                }
            });

            // Create charts
            createDoughnutChart('monthlyExpenseChart', monthlyDatas, 'Expenses by Category');
            createBarChart('monthlyIncomeChart', monthlyDatas, 'Monthly Income');
            createLineChart('monthlySavingsChart', monthlyDatas, 'Monthly Savings');

        });
    </script>
@endsection
