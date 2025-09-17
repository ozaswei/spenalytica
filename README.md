<h1 align="center">Spenalytica — Personal Finance Dashboard (Laravel)</h1>

<p align="center">
  A clean, responsive personal finance dashboard that helps you track <strong>income, expenses, subscriptions/MRR, categories, and budgets</strong>, visualize trends with Chart.js, and forecast your cashflow — all with elegant UI, animations, and empty-state safety.
</p>

<hr />

<h2>✨ Features</h2>
<ul>
  <li><strong>Categories</strong>: Create and manage custom categories with descriptions.</li>
  <li><strong>Expenses</strong>:
    <ul>
      <li>One-off expenses.</li>
      <li><strong>Subscriptions</strong> (<code>subscription = 1</code>) automatically recur monthly from <code>created_at</code> up to the current month.</li>
      <li>Span expenses across months when <code>updated_at &gt; created_at</code> (and <code>subscription = 0</code>).</li>
    </ul>
  </li>
  <li><strong>Income</strong>:
    <ul>
      <li>One-off income.</li>
      <li><strong>MRR</strong> (<code>mrr = 1</code>) treated as recurring for forecasting.</li>
    </ul>
  </li>
  <li><strong>Budgets</strong>: Per-category monthly budgets with progress bars (green / yellow / red).</li>
  <li><strong>Dashboard &amp; Visualizations</strong> (Chart.js):
    <ul>
      <li>Health Snapshot (net balance, average monthly Δ, latest health, <em>months until broke</em>).</li>
      <li>Monthly Expenses (doughnut), Monthly Income (bar with gradient aligned to the app background), Monthly Savings (line with gradient).</li>
      <li>Income vs Expenses (12 months).</li>
      <li><strong>Expenses by Category (subscription-aware pie)</strong>.</li>
      <li>Cashflow Forecast (next 6 months).</li>
    </ul>
  </li>
  <li><strong>Tables (DataTables)</strong>:
    <ul>
      <li>Expenses / Income with <strong>Month</strong> and <strong>Year</strong> filters (default <em>Show All</em>).</li>
      <li>Highest Expenses (search/length controls hidden for a clean card look).</li>
    </ul>
  </li>
  <li><strong>Savings Goal</strong>: Lightweight goal with progress (stored in session).</li>
  <li><strong>Great UX</strong>: Modern theme, subtle animations, fully responsive, and <strong>empty-state safe</strong> (no data → no crashes).</li>
</ul>

<hr />

<h2>🧩 Tech Stack</h2>
<ul>
  <li><strong>Backend</strong>: Laravel (PHP 8.1+)</li>
  <li><strong>Frontend</strong>: Blade, Bootstrap, Chart.js, jQuery + DataTables</li>
  <li><strong>Database</strong>: MySQL or PostgreSQL</li>
  <li><strong>Build</strong>: Laravel Vite (Node.js)</li>
</ul>

<hr />

<h2>📂 Project Structure (key parts)</h2>
<pre><code>app/
  Http/
    Controllers/
      ProfileController.php
app/Models/
  Budget.php
  Category.php
  Expense.php
  Income.php

resources/views/spenalytica/
  homePage.blade.php
  layouts/
    combiner.blade.php
    navbar.blade.php

routes/
  web.php
</code></pre>

<hr />

<h2>🗄️ Data Model (suggested)</h2>

<table>
  <tr>
    <th>Table</th>
    <th>Columns</th>
  </tr>
  <tr>
    <td><code>categories</code></td>
    <td><code>id</code>, <code>userId</code>, <code>category</code>, <code>description</code> (nullable), timestamps</td>
  </tr>
  <tr>
    <td><code>expenses</code></td>
    <td><code>id</code>, <code>userId</code>, <code>categoryId</code>, <code>expense</code>, <code>subscription</code> (0/1), <code>cost</code> (decimal), <code>description</code> (nullable), <code>created_at</code>, <code>updated_at</code></td>
  </tr>
  <tr>
    <td><code>incomes</code></td>
    <td><code>id</code>, <code>userId</code>, <code>categoryId</code>, <code>label</code>, <code>mrr</code> (0/1), <code>revenue</code> (decimal), <code>description</code> (nullable), <code>created_at</code>, <code>updated_at</code></td>
  </tr>
  <tr>
    <td><code>budgets</code></td>
    <td><code>id</code>, <code>userId</code>, <code>categoryId</code>, <code>amount</code> (decimal), timestamps</td>
  </tr>
</table>

<hr />

<h2>🧮 Core Logic</h2>

<h3>Expenses (Subscriptions)</h3>
<ul>
  <li><strong>If</strong> <code>subscription = 1</code>: expense recurs from <code>created_at</code> through the current month (inclusive).</li>
  <li><strong>Else if</strong> <code>subscription = 0</code> <em>and</em> <code>updated_at &gt; created_at</code>: expense spans from <code>created_at</code> through <code>updated_at</code> (inclusive).</li>
  <li><strong>Else</strong>: counted only in the <code>created_at</code> month.</li>
</ul>

<h3>Income (MRR)</h3>
<ul>
  <li><strong>If</strong> <code>mrr = 1</code>: counted as recurring for forecasting.</li>
  <li>Otherwise: contributes to actuals and to the variable average.</li>
</ul>

<h3>“Months Until Broke”</h3>
<ol>
  <li>Compute recurring parts:
    <ul>
      <li><code>recurringIncome = sum(income where mrr = 1)</code></li>
      <li><code>recurringExpense = sum(expense where subscription = 1)</code></li>
    </ul>
  </li>
  <li>Compute robust averages (trimmed mean) from historical monthly series:
    <ul>
      <li><code>variableIncomeAvg = trimmedMean(monthlyIncome) - recurringIncome</code></li>
      <li><code>variableExpenseAvg = trimmedMean(monthlyExpense) - recurringExpense</code></li>
    </ul>
  </li>
  <li><strong>ProjectedMonthlySavings</strong> = <code>(recurringIncome - recurringExpense) + (variableIncomeAvg - variableExpenseAvg)</code></li>
  <li>If negative: <strong>MonthsUntilBroke</strong> = <code>ceil(currentBalance / abs(ProjectedMonthlySavings))</code>; else <code>null</code>.</li>
</ol>

<hr />

<h2>🛠️ Installation</h2>

<h3>Prerequisites</h3>
<ul>
  <li>PHP 8.1+</li>
  <li>Composer</li>
  <li>Node.js (LTS)</li>
  <li>MySQL or PostgreSQL</li>
</ul>

<h3>Steps</h3>
<pre><code>git clone &lt;your-repo-url&gt; spenalytica
cd spenalytica

cp .env.example .env
composer install
php artisan key:generate
</code></pre>

<p>Configure your database in <code>.env</code>:</p>
<pre><code>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spenalytica
DB_USERNAME=root
DB_PASSWORD=secret
</code></pre>

<pre><code>php artisan migrate
# php artisan db:seed  # if you have seeders

npm install
npm run build   # or: npm run dev

php artisan serve
</code></pre>

<p>Open: <code>http://127.0.0.1:8000</code></p>

<hr />

<h2>🧭 Routes (examples)</h2>
<ul>
  <li><code>GET /</code> → Dashboard (<code>ProfileController@homePage</code>)</li>
  <li><code>POST /set-savings-goal</code> → Save goal (session)</li>
  <li><code>GET /forecast-data</code> → Forecast JSON</li>
  <li>Categories: <code>POST /category/add</code>, <code>/category/edit</code>, <code>/category/delete</code></li>
  <li>Expenses: <code>POST /expense/add</code>, <code>/expense/edit</code>, <code>/expense/delete</code></li>
  <li>Income: <code>POST /income/add</code>, <code>/income/edit</code>, <code>/income/delete</code></li>
  <li>Budgets: <code>POST /budgets</code></li>
</ul>

<hr />

<h2>🖥️ UI/UX Notes</h2>
<ul>
  <li>Animated charts with gradient fills; bar colors align with the page’s green→blue background.</li>
  <li>Tabs: Overview / Add Expense / Add Income / Budgets / Categories.</li>
  <li>DataTables: Month &amp; Year filters (default <em>Show All</em>), hidden search/length controls on specific tables.</li>
  <li>Modals for editing Category / Expense / Income, and setting Savings Goal.</li>
  <li><strong>Empty-state safety</strong>: no data yields empty datasets (no JS/PHP errors).</li>
</ul>

<hr />

<h2>🧪 Sample ENV (snippet)</h2>
<pre><code>APP_NAME=Spenalytica
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

LOG_CHANNEL=stack
LOG_LEVEL=debug
</code></pre>

<hr />

<h2>🩺 Troubleshooting</h2>
<ul>
  <li><strong>Blank charts / errors</strong>: Ensure Chart.js and DataTables assets are loaded (via Vite or CDN) and that controllers pass the expected props even when data is empty.</li>
  <li><strong>Migrations</strong>: Verify database connectivity and run <code>php artisan migrate:fresh</code> if schemas changed.</li>
  <li><strong>Permissions</strong>: Check storage permissions for Laravel (<code>storage/</code> &amp; <code>bootstrap/cache</code> writable).</li>
</ul>

<hr />

<h2>🤝 Contributing</h2>
<ol>
  <li>Fork the repo &amp; create a feature branch.</li>
  <li>Make your changes with tests/linters where applicable.</li>
  <li>Open a Pull Request with a clear description and screenshots/gifs.</li>
</ol>

<hr />

<h2>📜 License</h2>
<p>MIT — feel free to use in personal or commercial projects. Attribution appreciated but not required.</p>

<hr />

<h2>📧 Contact</h2>
<p>Questions or suggestions? Open an issue or reach out via your preferred channel.</p>
