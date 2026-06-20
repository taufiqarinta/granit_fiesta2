<nav x-data="{ open: false }"
     style="
        background-image: url('{{ asset('bg-login.png') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: sticky;
        top: 0;
        z-index: 50;
     ">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-9xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('welcome') }}">
                        {{-- <x-application-logo class="block w-auto text-white fill-current h-9" /> --}}
                        <img src="{{ asset('logo-kobin-one.png') }}" alt="Logo Kobin" class="w-24 h-10">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')"
                        class="text-white"
                        onmouseover="this.style.color='#dc2626'"
                        onmouseout="this.style.color='white'">
                        {{ __('Welcome') }}
                    </x-nav-link>
                </div>
                @if (Auth::user()->role_as == 1)
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.*')"
                            class="text-white"
                            onmouseover="this.style.color='#dc2626'"
                            onmouseout="this.style.color='white'">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:ms-10 granitfiesta">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white focus:outline-none transition ease-in-out duration-150 {{ request()->routeIs('daftartoko.*') ? 'text-white' : '' }}"
                                    onmouseover="this.style.color='#dc2626'"
                                    onmouseout="this.style.color='white'">
                                    <div>{{ __('Master Data') }}</div>

                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('master-lokasi-event.index')">
                                    {{ __('Master Lokasi Event') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('mastertarget.index')">
                                    {{ __('Master Paket') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('daftaragen.index')">
                                    {{ __('Daftar Agen') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('daftartoko.index')">
                                    {{ __('Daftar Toko') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('kehadiran.index')" :active="request()->routeIs('kehadiran.index')"
                            class="text-white"
                            onmouseover="this.style.color='#dc2626'"
                            onmouseout="this.style.color='white'">
                            {{ __('Daftar Kehadiran') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('form-survey.index')" :active="request()->routeIs('form-survey.*')"
                            class="text-white"
                            onmouseover="this.style.color='#dc2626'"
                            onmouseout="this.style.color='white'">
                            {{ __('Form Survey') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('form-order.index')" :active="request()->routeIs('form-order.*')"
                            class="text-white"
                            onmouseover="this.style.color='#dc2626'"
                            onmouseout="this.style.color='white'">
                            {{ __('Form Order') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('daftartoko.rekapan-gabungan')" :active="request()->routeIs('daftartoko.rekapan-gabungan')"
                            class="text-white"
                            onmouseover="this.style.color='#dc2626'"
                            onmouseout="this.style.color='white'">
                            {{ __('Rekap Kehadiran & Order') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('peringkat.index')" :active="request()->routeIs('peringkat.*')"
                            class="text-white"
                            onmouseover="this.style.color='#dc2626'"
                            onmouseout="this.style.color='white'">
                            {{ __('Peringkat') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('pemenang.list')" :active="request()->routeIs('pemenang.*')"
                            class="text-white"
                            onmouseover="this.style.color='#dc2626'"
                            onmouseout="this.style.color='white'">
                            {{ __('Klaim Doorprize') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('history-form-order.index')" :active="request()->routeIs('history-form-order.*')"
                            class="text-white"
                            onmouseover="this.style.color='#dc2626'"
                            onmouseout="this.style.color='white'">
                            {{ __('History Form Order') }}
                        </x-nav-link>
                    </div>
                    <!-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('order-gathering.index')" :active="request()->routeIs('order-gathering.*')">
                            {{ __('Order Gathering') }}
                        </x-nav-link>
                    </div> -->
                    <!-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('admin.customer')" :active="request()->routeIs('admin.customer')">
                            {{ __('Customer') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('admin.merk')" :active="request()->routeIs('admin.merk')">
                            {{ __('Merk') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('admin.ukuran')" :active="request()->routeIs('admin.ukuran')">
                            {{ __('Ukuran') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('admin.motif')" :active="request()->routeIs('admin.motif')">
                            {{ __('Motif') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('admin.transaksi')" :active="request()->routeIs('admin.transaksi')">
                            {{ __('Forecast Order') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('komplain.index')" :active="request()->routeIs('komplain.index')">
                            {{ __('Komplain') }}
                        </x-nav-link>
                    </div> -->
                    
                    <!-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('admin.log')" :active="request()->routeIs('admin.log')">
                            {{ __('Log Aktivitas') }}
                        </x-nav-link>
                    </div> -->
                @elseif (Auth::user()->role_as == 0)
                    <!-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('daftartoko.index')" :active="request()->routeIs('daftartoko.*')">
                            {{ __('Daftar Toko') }}
                        </x-nav-link>
                    </div> -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('form-order.index')" :active="request()->routeIs('form-order.*')"
                            class="text-white"
                            onmouseover="this.style.color='#dc2626'"
                            onmouseout="this.style.color='white'">
                            {{ __('Form Order') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('daftartoko.rekapan-gabungan')" :active="request()->routeIs('daftartoko.rekapan-gabungan')"
                            class="text-white"
                            onmouseover="this.style.color='#dc2626'"
                            onmouseout="this.style.color='white'">
                            {{ __('Rekap Kehadiran & Order') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('peringkat.index')" :active="request()->routeIs('peringkat.*')"
                            class="text-white"
                            onmouseover="this.style.color='#dc2626'"
                            onmouseout="this.style.color='white'">
                            {{ __('Peringkat') }}
                        </x-nav-link>
                    </div>
                @endif
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out text-white border border-transparent rounded-md focus:outline-none"
                                onmouseover="this.style.color='#dc2626'"
                                onmouseout="this.style.color='white'">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Update Profile & Password') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="flex items-center -me-2 sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 text-white transition duration-150 ease-in-out rounded-md focus:outline-none"
                        onmouseover="this.style.color='#dc2626'"
                        onmouseout="this.style.color='white'">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')"
                style="{{ request()->routeIs('welcome') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                onmouseover="this.style.color='#dc2626'"
                onmouseout="this.style.color='{{ request()->routeIs('welcome') ? '#dc2626' : 'white' }}'">
                {{ __('Welcome') }}
            </x-responsive-nav-link>

            @if (Auth::user()->role_as == 1)
                <x-responsive-nav-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.*')"
                    style="{{ request()->routeIs('dashboard.*') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('dashboard.*') ? '#dc2626' : 'white' }}'">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('master-lokasi-event.index')" :active="request()->routeIs('master-lokasi-event.*')"
                    style="{{ request()->routeIs('master-lokasi-event.*') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('master-lokasi-event.*') ? '#dc2626' : 'white' }}'">
                    {{ __('Master Lokasi Event') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('mastertarget.index')" :active="request()->routeIs('mastertarget.*')"
                    style="{{ request()->routeIs('mastertarget.*') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('mastertarget.*') ? '#dc2626' : 'white' }}'">
                    {{ __('Master Paket') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('daftaragen.index')" :active="request()->routeIs('daftaragen.*')"
                    style="{{ request()->routeIs('daftaragen.*') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('daftaragen.*') ? '#dc2626' : 'white' }}'">
                    {{ __('Daftar Agen') }}
                </x-responsive-nav-link>
                <!-- <x-responsive-nav-link :href="route('daftartoko.index')" :active="request()->routeIs('daftartoko.*')"
                    style="{{ request()->routeIs('daftartoko.*') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('daftartoko.*') ? '#dc2626' : 'white' }}'">
                    {{ __('Daftar Toko') }}
                </x-responsive-nav-link> -->
                <x-responsive-nav-link :href="route('daftartoko.index')" :active="request()->routeIs('daftartoko.index') || request()->routeIs('daftartoko.create') || request()->routeIs('daftartoko.edit') || request()->routeIs('daftartoko.show')"
                    style="{{ (request()->routeIs('daftartoko.index') || request()->routeIs('daftartoko.create') || request()->routeIs('daftartoko.edit') || request()->routeIs('daftartoko.show')) ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ (request()->routeIs('daftartoko.index') || request()->routeIs('daftartoko.create') || request()->routeIs('daftartoko.edit') || request()->routeIs('daftartoko.show')) ? '#dc2626' : 'white' }}'">
                    {{ __('Daftar Toko') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('kehadiran.index')" :active="request()->routeIs('kehadiran.*')"
                    style="{{ request()->routeIs('kehadiran.*') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('kehadiran.*') ? '#dc2626' : 'white' }}'">
                    {{ __('Daftar Kehadiran') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('form-survey.index')" :active="request()->routeIs('form-survey.*')"
                    style="{{ request()->routeIs('form-survey.*') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('form-survey.*') ? '#dc2626' : 'white' }}'">
                    {{ __('Form Survey') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('form-order.index')" :active="request()->routeIs('form-order.*')"
                    style="{{ request()->routeIs('form-order.*') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('form-order.*') ? '#dc2626' : 'white' }}'">
                    {{ __('Form Order') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('daftartoko.rekapan-gabungan')" :active="request()->routeIs('daftartoko.rekapan-gabungan')"
                    style="{{ request()->routeIs('daftartoko.rekapan-gabungan') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('daftartoko.rekapan-gabungan') ? '#dc2626' : 'white' }}'">
                    {{ __('Rekap Kehadiran & Order') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('peringkat.index')" :active="request()->routeIs('peringkat.*')"
                    style="{{ request()->routeIs('peringkat.*') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('peringkat.*') ? '#dc2626' : 'white' }}'">
                    {{ __('Peringkat') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pemenang.list')" :active="request()->routeIs('pemenang.*')"
                    style="{{ request()->routeIs('pemenang.*') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('pemenang.*') ? '#dc2626' : 'white' }}'">
                    {{ __('Klaim Doorprize') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('history-form-order.index')" :active="request()->routeIs('history-form-order.*')"
                    style="{{ request()->routeIs('history-form-order.*') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('history-form-order.*') ? '#dc2626' : 'white' }}'">
                    {{ __('History Order') }}
                </x-responsive-nav-link>
            @elseif (Auth::user()->role_as == 0)
                <x-responsive-nav-link :href="route('form-order.index')" :active="request()->routeIs('form-order.*')"
                    style="{{ request()->routeIs('form-order.*') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('form-order.*') ? '#dc2626' : 'white' }}'">
                    {{ __('Form Order') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('daftartoko.rekapan-gabungan')" :active="request()->routeIs('daftartoko.rekapan-gabungan')"
                    style="{{ request()->routeIs('daftartoko.rekapan-gabungan') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('daftartoko.rekapan-gabungan') ? '#dc2626' : 'white' }}'">
                    {{ __('Rekap Kehadiran & Order') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('peringkat.index')" :active="request()->routeIs('peringkat.*')"
                    style="{{ request()->routeIs('peringkat.*') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('peringkat.*') ? '#dc2626' : 'white' }}'">
                    {{ __('Peringkat') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-white">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')"
                    :active="request()->routeIs('profile.edit')"
                    style="{{ request()->routeIs('profile.edit') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                    onmouseover="this.style.color='#dc2626'"
                    onmouseout="this.style.color='{{ request()->routeIs('profile.edit') ? '#dc2626' : 'white' }}'">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        style="{{ request()->routeIs('logout') ? 'color: #dc2626 !important; border-color: #ef4444;' : 'color: white !important;' }}"
                        onmouseover="this.style.color='#dc2626'"
                        onmouseout="this.style.color='{{ request()->routeIs('logout') ? '#dc2626' : 'white' }}'">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
