<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">  
  <link rel="shortcut icon" href="{{ asset('images/fav.png') }}" type="image/x-icon">  
  <link rel="stylesheet" href="https://kit-pro.fontawesome.com/releases/v5.12.1/css/pro.min.css">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">  
  <title>Welcome To Cleopatra</title>
</head>
<body class="bg-gray-100">

<!-- start navbar -->
<div class="md:fixed md:w-full md:top-0 md:z-20 flex flex-row flex-wrap items-center bg-white p-6 border-b border-gray-300">
  <div class="flex-none w-56 flex flex-row items-center">
    <img src="{{ asset('images/logo.png') }}" class="w-10 flex-none">
    <strong class="capitalize ml-1 flex-1">cleopatra</strong>
  </div>
</div>
<!-- end navbar -->


<!-- start wrapper -->
<div class="h-screen flex flex-row flex-wrap">
  
  <!-- start sidebar -->
  <div class="relative flex flex-col flex-wrap bg-white border-r border-gray-300 p-6 flex-none w-64">
    <p class="uppercase text-xs text-gray-600 mb-4 tracking-wider">UI Elements</p>

    <a href="{{ route('alert') }}" class="mb-3 capitalize font-medium text-sm hover:text-teal-600 transition ease-in-out duration-500">
      <i class="fad fa-whistle text-xs mr-2"></i> Alerts
    </a>
  </div>
  <!-- end sidebar -->

  <!-- start content -->
  <div class="bg-gray-100 flex-1 p-6 md:mt-16">
    
    <h1 class="h5">Alerts</h1>
    <p>Gunakan gaya alert Cleopatra untuk aksi dan notifikasi.</p>
    <p>Saat ini ada 3 tipe alert yang bisa digunakan.</p>

    <hr class="my-5">

    <div class="card">
      <div class="card-header">
        Contoh alert dengan class <span class="badge badge-primary">.alert</span> dan <span class="badge badge-primary">.alert-(type)</span>
      </div>
      <div class="card-body">
        <div class="alert mb-5">A simple alert it out!</div>
        <div class="alert alert-default mb-5">A simple alert it out!</div>
        <div class="alert alert-light mb-5">A simple alert it out!</div>
        <div class="alert alert-dark mb-5">A simple alert it out!</div>
        <div class="alert alert-success mb-5">A simple alert it out!</div>
        <div class="alert alert-error">A simple alert it out!</div>
      </div>
    </div>

  </div>
  <!-- end content -->
</div>
<!-- end wrapper -->

<!-- script -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('js/scripts.js') }}"></script>
<!-- end script -->

</body>
</html>
