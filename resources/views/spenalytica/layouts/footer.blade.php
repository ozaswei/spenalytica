    <footer class="footer">
        &copy; 2025 Spenalytica. Spend smarter. Live better.
    </footer>
    <!-- bootstaps -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous">
    </script>
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
