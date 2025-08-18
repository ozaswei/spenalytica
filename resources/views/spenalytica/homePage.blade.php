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
    .modal {
    z-index: 1 !important;
    }
    .modal-backdrop {
    z-index: -1 !important;
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
                <div class="row">
                    <div class="col-md-6">
                        <div class="chart-container">
                            <canvas id="expenseChart" width="30" height="30"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        
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
                            <input type="month" id="expenseMonthFilter" name="expenseMonthFilter" class="form-control">
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
                                                        <input type="hidden" value="{{ $expense->id }}" name="expenseId">
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
    <script>
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

            //Chart JS
            // Convert Laravel data to JS
            const expenses = @json($expenses);
            console.log(expenses);
            const incomes = @json($incomes);
            const categories = @json($categories);
            // Format expenses by category for chart
            const expenseList = expenses.map(exp => ({
                expense: exp.expense,
                cost: parseFloat(exp.cost)
            }));
            console.log(expenseList);

            // Format incomes by category for chart
            const incomeData = {};
            incomes.forEach(inc => {
                const cat = categories.find(c => c.id === inc.categoryId)?.category || 'Uncategorized';
                incomeData[cat] = (incomeData[cat] || 0) + parseFloat(inc.revenue);
            });
            //doughnut chart

            // Helper to create Doughnut chart
            function createDoughnutChart(canvasId, data, label) {
                const ctx = document.getElementById(canvasId).getContext('2d');
                return new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: expenseList.map(e => e.expense),
                        datasets: [{
                            label: "Pie",
                            data: expenseList.map(e => e.cost),
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

            // Create charts
            createDoughnutChart('expenseChart', expenseList, 'Expenses by Category');
            //createDoughnutChart('incomeChart', incomeData, 'Incomes by Category');

        });
    </script>
@endsection
