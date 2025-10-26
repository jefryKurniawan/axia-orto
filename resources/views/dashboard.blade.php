{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">  
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{ asset('img/fav.png') }}" type="image/x-icon">  
  <link rel="stylesheet" href="https://kit-pro.fontawesome.com/releases/v5.12.1/css/pro.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/icons.css') }}">
  <title>Dashboard - Klinik Ortopedi</title>
</head>
<body class="bg-gray-100">

<!-- start navbar -->
<div class="md:fixed md:w-full md:top-0 md:z-20 flex flex-row flex-wrap items-center bg-white p-6 border-b border-gray-300">
    
    <!-- logo -->
    <div class="flex-none w-56 flex flex-row items-center">
      <img src="{{ asset('images/logo.png') }}" alt="Klinik Ortopedi Logo" class="h-10">
      <strong class="capitalize ml-2 text-blue-600">Klinik Ortopedi</strong>

      <button id="sliderBtn" class="flex-none text-right text-gray-900 hidden md:block">
        <i class="fad fa-list-ul"></i>
      </button>
    </div>
    <!-- end logo -->   
    
    <!-- navbar content toggle -->
    <button id="navbarToggle" class="hidden md:block md:fixed right-0 mr-6">
      <i class="fad fa-chevron-double-down"></i>
    </button>
    <!-- end navbar content toggle -->

    <!-- navbar content -->
    <div id="navbar" class="animated md:hidden md:fixed md:top-0 md:w-full md:left-0 md:mt-16 md:border-t md:border-b md:border-gray-200 md:p-10 md:bg-white flex-1 pl-3 flex flex-row flex-wrap justify-between items-center md:flex-col md:items-center">
      
      <!-- right -->
      <div class="flex flex-row-reverse items-center"> 

        <!-- user -->
        <div class="dropdown relative md:static">

          <button class="menu-btn focus:outline-none focus:shadow-outline flex flex-wrap items-center">
            <div class="w-8 h-8 overflow-hidden rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
              {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div> 

            <div class="ml-2 capitalize flex ">
              <h1 class="text-sm text-gray-800 font-semibold m-0 p-0 leading-none">{{ auth()->user()->name }}</h1>
              <i class="fad fa-chevron-down ml-2 text-xs leading-none"></i>
            </div>                        
          </button>

          <button class="hidden fixed top-0 left-0 z-10 w-full h-full menu-overflow"></button>

          <div class="text-gray-500 menu hidden md:mt-10 md:w-full rounded bg-white shadow-md absolute z-20 right-0 w-40 mt-5 py-2 animated faster">

            <!-- item -->
            <a class="px-4 py-2 block capitalize font-medium text-sm tracking-wide bg-white hover:bg-gray-200 hover:text-gray-900 transition-all duration-300 ease-in-out" href="#">
              <i class="fad fa-user-edit text-xs mr-1"></i> 
              Edit Profil
            </a>     
            <!-- end item -->

            <!-- item -->
            <a class="px-4 py-2 block capitalize font-medium text-sm tracking-wide bg-white hover:bg-gray-200 hover:text-gray-900 transition-all duration-300 ease-in-out" href="#">
              <i class="fad fa-cog text-xs mr-1"></i> 
              Pengaturan
            </a>     
            <!-- end item -->

            <hr>

            <!-- item -->
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="px-4 py-2 block capitalize font-medium text-sm tracking-wide bg-white hover:bg-gray-200 hover:text-gray-900 transition-all duration-300 ease-in-out w-full text-left">
                <i class="fad fa-sign-out text-xs mr-1"></i> 
                Logout
              </button>   
            </form>
            <!-- end item -->

          </div>
        </div>
        <!-- end user -->

      </div>
      <!-- end right -->
    </div>
    <!-- end navbar content -->

  </div>
<!-- end navbar -->

<!-- strat wrapper -->
<div class="h-screen flex flex-row flex-wrap">
  
    <!-- start sidebar -->
  <div id="sideBar" class="relative flex flex-col flex-wrap bg-white border-r border-gray-300 p-6 flex-none w-64 md:-ml-64 md:fixed md:top-0 md:z-30 md:h-screen md:shadow-xl animated faster">
    

    <!-- sidebar content -->
    <div class="flex flex-col">

      <!-- sidebar toggle -->
      <div class="text-right hidden md:block mb-4">
        <button id="sideBarHideBtn">
          <i class="fad fa-times-circle"></i>
        </button>
      </div>
      <!-- end sidebar toggle -->

      <p class="uppercase text-xs text-gray-600 mb-4 tracking-wider">Menu Utama</p>

      <!-- link -->
      <a href="{{ route('dashboard') }}" class="mb-3 capitalize font-medium text-sm hover:text-blue-600 transition ease-in-out duration-500 {{ request()->routeIs('dashboard') ? 'text-blue-600' : '' }}">
        <i class="fad fa-chart-pie text-xs mr-2"></i>                
        Dashboard
      </a>
      <!-- end link -->

      <p class="uppercase text-xs text-gray-600 mb-4 mt-4 tracking-wider">Manajemen Klinik</p>

      <!-- link -->
      <a href="{{ route('patients.index') }}" class="mb-3 capitalize font-medium text-sm hover:text-blue-600 transition ease-in-out duration-500 {{ request()->routeIs('patients.*') ? 'text-blue-600' : '' }}">
        <i class="fad fa-user-injured text-xs mr-2"></i>
        Manajemen Pasien
      </a>
      <!-- end link -->

      <!-- link -->
      <a href="{{ route('consultations.index') }}" class="mb-3 capitalize font-medium text-sm hover:text-blue-600 transition ease-in-out duration-500 {{ request()->routeIs('consultations.*') ? 'text-blue-600' : '' }}">
        <i class="fad fa-stethoscope text-xs mr-2"></i>
        Konsultasi
      </a>
      <!-- end link -->

      <!-- link -->
      <a href="{{ route('services.index') }}" class="mb-3 capitalize font-medium text-sm hover:text-blue-600 transition ease-in-out duration-500 {{ request()->routeIs('services.*') ? 'text-blue-600' : '' }}">
        <i class="fad fa-cogs text-xs mr-2"></i>
        Layanan & Harga
      </a>
      <!-- end link -->

      <!-- link -->
      <a href="{{ route('treatment-orders.index') }}" class="mb-3 capitalize font-medium text-sm hover:text-blue-600 transition ease-in-out duration-500 {{ request()->routeIs('treatment-orders.*') ? 'text-blue-600' : '' }}">
        <i class="fad fa-clipboard-list text-xs mr-2"></i>
        Pemesanan
      </a>
      <!-- end link -->

      <p class="uppercase text-xs text-gray-600 mb-4 mt-4 tracking-wider">Lainnya</p>

      <!-- link -->
      <a href="#" class="mb-3 capitalize font-medium text-sm hover:text-blue-600 transition ease-in-out duration-500">
        <i class="fad fa-users text-xs mr-2"></i>
        Staff Klinik
      </a>
      <!-- end link -->

      <!-- link -->
      <a href="#" class="mb-3 capitalize font-medium text-sm hover:text-blue-600 transition ease-in-out duration-500">
        <i class="fad fa-file-invoice text-xs mr-2"></i>
        Laporan
      </a>
      <!-- end link -->

      <!-- link -->
      <a href="#" class="mb-3 capitalize font-medium text-sm hover:text-blue-600 transition ease-in-out duration-500">
        <i class="fad fa-cog text-xs mr-2"></i>
        Pengaturan
      </a>
      <!-- end link -->

    </div>
    <!-- end sidebar content -->

  </div>
  <!-- end sidbar -->

  <!-- strat content -->
  <div class="bg-gray-100 flex-1 p-6 md:mt-16"> 

    <!-- Notifications -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="text-green-700 hover:text-green-900" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center justify-between">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" class="text-red-700 hover:text-red-900" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <!-- Welcome & Quick Stats -->
    <div class="grid grid-cols-3 lg:grid-cols-1 gap-5">
      
      <!-- Welcome Card -->
      <div class="card col-span-1">
        <div class="card-body h-full flex flex-col justify-between">
          <div>
            <h1 class="text-lg font-bold tracking-wide">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="text-gray-600 mt-2">Ringkasan aktivitas klinik hari ini</p>
          </div>
      
          <div class="flex flex-row mt-6 items-end">
            <div class="flex-1">
              <h1 class="font-extrabold text-3xl text-blue-600">{{ $stats['today_consultations'] }}</h1>
              <p class="mt-2 mb-3 text-xs text-gray-500">Konsultasi hari ini</p>
              <a href="{{ route('consultations.create') }}" class="btn-shadow py-2 px-4 text-sm">
                Buat Konsultasi Baru
              </a>
            </div>
            <div class="flex-1 ml-6 w-24 h-24 lg:w-auto lg:h-auto overflow-hidden">
              <img class="object-cover" src="{{ asset('img/medical-team.svg') }}">
            </div>
          </div>
        </div>
      </div>
      <!-- end Welcome Card -->

      <!-- Quick Stats -->
      <div class="card p-0 overflow-hidden col-span-2 lg:col-span-1 flex flex-row lg:flex-col">
        <div class="border-r border-gray-200 w-2/3 lg:w-full">
          <div class="p-5">
            <h2 class="font-bold text-lg">Statistik Cepat</h2>
          </div>
          
          <div class="grid grid-cols-2 gap-4 p-5">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
              <div class="text-2xl font-bold text-blue-600">{{ $stats['total_patients'] }}</div>
              <div class="text-sm text-gray-600 mt-1">Total Pasien</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
              <div class="text-2xl font-bold text-green-600">{{ $stats['active_orders'] }}</div>
              <div class="text-sm text-gray-600 mt-1">Pemesanan Aktif</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
              <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_actions'] }}</div>
              <div class="text-sm text-gray-600 mt-1">Perlu Tindakan</div>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
              <div class="text-2xl font-bold text-purple-600">
                {{ \Carbon\Carbon::now()->format('d M Y') }}
              </div>
              <div class="text-sm text-gray-600 mt-1">Hari Ini</div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="w-1/3 lg:w-full">
          <div class="p-5 border-b border-gray-200">
            <h2 class="font-bold text-lg mb-4">Aksi Cepat</h2>
            <a href="{{ route('patients.create') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded mb-3 transition-colors">
              <i class="fas fa-plus mr-2"></i>Pasien Baru
            </a>
            <a href="{{ route('consultations.create') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded mb-3 transition-colors">
              <i class="fas fa-stethoscope mr-2"></i>Konsultasi Baru
            </a>
            <a href="{{ route('treatment-orders.create') }}" class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center py-2 px-4 rounded transition-colors">
              <i class="fas fa-clipboard-list mr-2"></i>Pemesanan Baru
            </a>
          </div>
        </div>
      </div>
    </div>
    <!-- end Welcome & Quick Stats -->

    <!-- Statistics Cards -->
    <div class="grid grid-cols-5 gap-5 mt-5 lg:grid-cols-2">
      <!-- Today -->
      <div class="card col-span-1">
        <div class="card-body text-center">
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-calendar-day text-blue-600"></i>
          </div>
          <h5 class="uppercase text-xs tracking-wider font-extrabold text-gray-500">Hari Ini</h5>
          <h1 class="capitalize text-xl mt-1 mb-1 text-blue-600">{{ $stats['today_consultations'] }}</h1>
          <p class="capitalize text-xs text-gray-500">Konsultasi</p>
        </div>
      </div>

      <!-- This Week -->
      <div class="card col-span-1">
        <div class="card-body text-center">
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-calendar-week text-green-600"></i>
          </div>
          <h5 class="uppercase text-xs tracking-wider font-extrabold text-gray-500">Minggu Ini</h5>
          <h1 class="capitalize text-xl mt-1 mb-1 text-green-600">{{ $stats['week_consultations'] ?? '0' }}</h1>
          <p class="capitalize text-xs text-gray-500">Konsultasi</p>
        </div>
      </div>

      <!-- This Month -->
      <div class="card col-span-1">
        <div class="card-body text-center">
          <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-calendar-alt text-purple-600"></i>
          </div>
          <h5 class="uppercase text-xs tracking-wider font-extrabold text-gray-500">Bulan Ini</h5>
          <h1 class="capitalize text-xl mt-1 mb-1 text-purple-600">{{ $stats['month_consultations'] ?? '0' }}</h1>
          <p class="capitalize text-xs text-gray-500">Konsultasi</p>
        </div>
      </div>

      <!-- Active Orders -->
      <div class="card col-span-1">
        <div class="card-body text-center">
          <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-clipboard-check text-yellow-600"></i>
          </div>
          <h5 class="uppercase text-xs tracking-wider font-extrabold text-gray-500">Pemesanan</h5>
          <h1 class="capitalize text-xl mt-1 mb-1 text-yellow-600">{{ $stats['active_orders'] }}</h1>
          <p class="capitalize text-xs text-gray-500">Sedang Diproses</p>
        </div>
      </div>

      <!-- Total Patients -->
      <div class="card col-span-1 lg:col-span-2">
        <div class="card-body text-center">
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-user-injured text-red-600"></i>
          </div>
          <h5 class="uppercase text-xs tracking-wider font-extrabold text-gray-500">Total Pasien</h5>
          <h1 class="capitalize text-xl mt-1 mb-1 text-red-600">{{ $stats['total_patients'] }}</h1>
          <p class="capitalize text-xs text-gray-500">Terdaftar</p>
        </div>
      </div>
    </div>
    <!-- end Statistics Cards -->

    <!-- Recent Activity -->
    <div class="grid grid-cols-2 lg:grid-cols-1 gap-5 mt-5">
      
      <!-- Recent Consultations -->
      <div class="card">
        <div class="card-body">
          <div class="flex flex-row justify-between items-center mb-4">
            <h1 class="font-extrabold text-lg">Konsultasi Terbaru</h1>
            <a href="{{ route('consultations.index') }}" class="btn-gray text-sm">Lihat Semua</a>
          </div>
      
          <div class="space-y-4">
            @forelse($recentConsultations as $consultation)
            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                  <i class="fas fa-user-injured text-blue-600 text-sm"></i>
                </div>
                <div>
                  <h4 class="text-sm font-semibold text-gray-800">{{ $consultation->patient->name }}</h4>
                  <p class="text-xs text-gray-500">{{ Str::limit($consultation->complaint, 40) }}</p>
                </div>
              </div>
              <div class="text-right">
                <span class="inline-block px-2 py-1 text-xs rounded-full 
                  {{ $consultation->status == 'completed' ? 'bg-green-100 text-green-800' : 
                     ($consultation->status == 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 
                     ($consultation->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                  {{ Str::title(str_replace('_', ' ', $consultation->status)) }}
                </span>
                <p class="text-xs text-gray-500 mt-1">{{ $consultation->consultation_date->format('d M H:i') }}</p>
              </div>
            </div>
            @empty
            <div class="text-center py-6 text-gray-500">
              <i class="fas fa-calendar-times text-3xl mb-2"></i>
              <p>Tidak ada konsultasi terbaru</p>
            </div>
            @endforelse
          </div>
        </div>
      </div>

      <!-- Recent Patients -->
      <div class="card">    
        <div class="card-body">
          <div class="flex flex-row justify-between items-center mb-4">
            <h2 class="font-bold text-lg">Pasien Terbaru</h2>
            <a href="{{ route('patients.index') }}" class="btn-gray text-sm">Lihat Semua</a>
          </div>
          
          <div class="space-y-4">
            @forelse($recentPatients as $patient)
            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                  <i class="fas fa-user text-green-600 text-sm"></i>
                </div>
                <div>
                  <h4 class="text-sm font-semibold text-gray-800">{{ $patient->name }}</h4>
                  <p class="text-xs text-gray-500">MRN: {{ $patient->medical_record_number }}</p>
                </div>
              </div>
              <div class="text-right">
                <span class="inline-block px-2 py-1 text-xs rounded-full 
                  {{ $patient->insurance_type == 'bpjs' ? 'bg-green-100 text-green-800' : 
                     ($patient->insurance_type == 'asuransi' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                  {{ Str::title($patient->insurance_type) }}
                </span>
                <p class="text-xs text-gray-500 mt-1">{{ $patient->created_at->format('d M Y') }}</p>
              </div>
            </div>
            @empty
            <div class="text-center py-6 text-gray-500">
              <i class="fas fa-users text-3xl mb-2"></i>
              <p>Belum ada pasien terdaftar</p>
            </div>
            @endforelse
          </div>
        </div>
      </div> 
    </div>
    <!-- end Recent Activity -->

    <!-- Upcoming Schedule -->
    <div class="card mt-5">
      <div class="card-body">
        <div class="flex flex-row justify-between items-center mb-4">
          <h2 class="font-bold text-lg">Jadwal Mendatang</h2>
          <a href="#" class="btn-gray text-sm">Lihat Kalender</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Today's Schedule -->
          <div class="bg-blue-50 p-4 rounded-lg">
            <h3 class="font-semibold text-blue-800 mb-3">Hari Ini</h3>
            @forelse($todaySchedule as $schedule)
            <div class="flex items-center justify-between mb-3 p-2 bg-white rounded">
              <div>
                <p class="text-sm font-medium">{{ $schedule->patient->name }}</p>
                <p class="text-xs text-gray-500">{{ $schedule->consultation_date->format('H:i') }}</p>
              </div>
              <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Konsultasi</span>
            </div>
            @empty
            <p class="text-sm text-gray-500 text-center py-2">Tidak ada jadwal</p>
            @endforelse
          </div>

          <!-- Tomorrow's Schedule -->
          <div class="bg-green-50 p-4 rounded-lg">
            <h3 class="font-semibold text-green-800 mb-3">Besok</h3>
            @forelse($tomorrowSchedule as $schedule)
            <div class="flex items-center justify-between mb-3 p-2 bg-white rounded">
              <div>
                <p class="text-sm font-medium">{{ $schedule->patient->name }}</p>
                <p class="text-xs text-gray-500">{{ $schedule->consultation_date->format('H:i') }}</p>
              </div>
              <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Konsultasi</span>
            </div>
            @empty
            <p class="text-sm text-gray-500 text-center py-2">Tidak ada jadwal</p>
            @endforelse
          </div>

          <!-- Follow-ups -->
          <div class="bg-yellow-50 p-4 rounded-lg">
            <h3 class="font-semibold text-yellow-800 mb-3">Tindak Lanjut</h3>
            @forelse($followUps as $followUp)
            <div class="flex items-center justify-between mb-3 p-2 bg-white rounded">
              <div>
                <p class="text-sm font-medium">{{ $followUp->patient->name }}</p>
                <p class="text-xs text-gray-500">{{ $followUp->follow_up_date->format('d M') }}</p>
              </div>
              <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Tindak Lanjut</span>
            </div>
            @empty
            <p class="text-sm text-gray-500 text-center py-2">Tidak ada tindak lanjut</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
    <!-- end Upcoming Schedule -->

  </div>
  <!-- end content -->

</div>
<!-- end wrapper -->

<!-- script -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/scripts.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>
<!-- end script -->

<script>
// Auto-hide notifications after 5 seconds
setTimeout(() => {
    const notifications = document.querySelectorAll('.bg-green-50, .bg-red-50');
    notifications.forEach(notification => notification.remove());
}, 5000);
</script>

</body>
</html>
