@if (session('success'))
    <div class="alert alert-success" role="alert" id="successAlert">
        {{ session('success') }}
    </div>
@endif

@if (session('failed'))
    <div class="alert alert-danger" role="alert" id="failedAlert">
        {{ session('failed') }}
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning" role="alert" id="warningAlert">
        {{ session('warning') }}
    </div>
@endif
