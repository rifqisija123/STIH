<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Pemetaan - STIH')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo_stih_white.png') }}">
    
    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#b2202c',
                            hover: '#9a1b25',
                            dark: '#821620'
                        }
                    },
                    fontFamily: {
                        'nunito': ['Nunito', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <style>
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .modern-navbar {
            background: linear-gradient(135deg, #b2202c 0%, #9a1b25 50%, #821620 100%);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('styles')

</head>

<body id="page-top" class="bg-gray-100 font-nunito">

    <!-- Page Wrapper -->
    <div id="page-wrapper" class="min-h-screen flex flex-col">

        <!-- Navigation -->
        @include('layouts.partials.navbar')

        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-6 lg:p-8 pt-24">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white py-4 border-t border-gray-200">
            <div class="container mx-auto px-4">
                <p class="text-center text-gray-600 text-sm">
                    © {{ date('Y') }} STIH Adhyaksa - Pemetaan Intern
                </p>
            </div>
        </footer>

    </div>

    <!-- Scroll to Top Button -->
    <a class="fixed bottom-4 right-4 w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center shadow-lg hover:bg-primary-hover transition-all duration-200 hidden" href="#page-top" id="scrollToTop">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')

    <script>
        // Scroll to top functionality
        $(document).ready(function() {
            $(window).scroll(function() {
                if ($(this).scrollTop() > 200) {
                    $('#scrollToTop').removeClass('hidden');
                } else {
                    $('#scrollToTop').addClass('hidden');
                }
            });

            $('#scrollToTop').click(function(e) {
                e.preventDefault();
                $('html, body').animate({scrollTop: 0}, 500);
            });
        });
    </script>

</body>

</html>
