    <footer class="footer">
        &copy; 2025 Spenalytica. Spend smarter. Live better.
    </footer>

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- bootstaps -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
        integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK" crossorigin="anonymous">
    </script>

    <!-- chart js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Navbar Js -->
    <script src="{{ asset('js/navbar.js') }}"></script>

    <!-- Alert Js-->
    <script>
        setTimeout(function() {
            var alertElement = document.getElementById('successAlert');
            if (alertElement) {
                alertElement.style.display = 'none'; // Or alertElement.remove();
            }
        }, 2000); // 2000 milliseconds = 2 seconds
        setTimeout(function() {
            var alertElement = document.getElementById('failedAlert');
            if (alertElement) {
                alertElement.style.display = 'none'; // Or alertElement.remove();
            }
        }, 2000); // 2000 milliseconds = 2 seconds
        setTimeout(function() {
            var alertElement = document.getElementById('warningAlert');
            if (alertElement) {
                alertElement.style.display = 'none'; // Or alertElement.remove();
            }
        }, 2000); // 2000 milliseconds = 2 seconds
    </script>
    <!-- custom Js -->
    @include('spenalytica.layouts.javascripts')
    </body>

    </html>
