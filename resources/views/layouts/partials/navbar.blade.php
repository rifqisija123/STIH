<nav class="modern-navbar fixed top-0 left-0 right-0 z-50 shadow-lg">
    <div class="w-full px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Left: Brand -->
            <a class="flex items-center space-x-3 text-white no-underline" href="{{ url('/') }}">
                <div class="bg-white bg-opacity-10 p-2 rounded-xl">
                    <img src="{{ asset('images/logo_stih_white.png') }}" alt="STIH Logo" width="40" height="40" class="filter drop-shadow-lg">
                </div>
                <div>
                    <span class="block text-xl font-bold leading-5">STIH Adhyaksa</span>
                    <small class="block text-xs text-white text-opacity-80">Pemetaan</small>
                </div>
            </a>

            <!-- Center: Navigation Menu -->
            <div class="hidden md:flex items-center space-x-2">
                <a class="flex items-center px-4 py-2 rounded-lg text-white text-opacity-90 font-medium text-sm transition-all duration-200 {{ request()->routeIs('pemetaan.form') && !request()->routeIs('pemetaan.form.tabel') && !request()->routeIs('pemetaan.import') ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' }}" href="{{ route('pemetaan.form') }}">
                    <i class="fas fa-edit mr-2"></i>
                    Form Input
                </a>
                <a class="flex items-center px-4 py-2 rounded-lg text-white text-opacity-90 font-medium text-sm transition-all duration-200 {{ request()->routeIs('pemetaan.form.tabel') ? 'bg-white bg-opacity-20' : 'hover:bg-white hover:bg-opacity-10' }}" href="{{ route('pemetaan.form.tabel') }}">
                    <i class="fas fa-table mr-2"></i>
                    Tabel Data
                </a>
            </div>

            <!-- Right: User Profile -->
            <div class="flex items-center space-x-2">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center px-3 py-2 rounded-lg text-white bg-white bg-opacity-10 hover:bg-opacity-20 transition-all duration-200">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm mr-2">
                            @php
                                $userName = session('user.name', 'User');
                                $initials = collect(explode(' ', $userName))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('');
                            @endphp
                            {{ $initials }}
                        </div>
                        <span class="hidden md:block text-sm font-medium">{{ session('user.name', 'User') }}</span>
                        <i class="fas fa-chevron-down ml-2 text-xs opacity-70"></i>
                    </button>
                    
                    <div x-show="open" x-cloak @click.away="open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-2">
                        
                        <div class="px-4 py-2 border-b border-gray-100">
                            <div class="font-semibold text-gray-900">{{ session('user.name', 'User') }}</div>
                            <div class="text-sm text-gray-500">{{ session('user.email', 'user@example.com') }}</div>
                        </div>
                        
                        <!-- Mobile Menu Links -->
                        <div class="md:hidden border-b border-gray-100 py-2">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-tachometer-alt mr-2 w-4"></i> Dashboard
                            </a>
                            <a href="{{ route('pemetaan.form') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-edit mr-2 w-4"></i> Form Input
                            </a>
                            <a href="{{ route('pemetaan.form.tabel') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-table mr-2 w-4"></i> Tabel Data
                            </a>
                        </div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
