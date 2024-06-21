<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon" style="height: 70px">
            {{-- <i class="fas fa-laugh-wink"></i> --}}
            <img class="h-100" style="object-fit: scale-down" src="{{ asset('img/bidan2.png') }}" alt="...">
        </div>
        <div class="sidebar-brand-text mx-3">Bidan X</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Request::is('/home') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('home.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    {{-- <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Data Pasien
    </div> --}}


    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Pelayanan
    </div>
    <li class="nav-item {{ Request::is('kb*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kb.index') }}">
            <i class="fas fa-user-friends"></i>
            <span>KB</span></a>
    </li>
    <li class="nav-item {{ Request::is('kehamilan*') ? 'active' : '' }}">
        <a class="nav-link " href="{{ route('kehamilan.index') }}">
            <i class="fas fa-user-nurse"></i>
            <span>Kehamilan</span></a>
    </li>
    <li class="nav-item {{ Request::is('persalinan*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('persalinan.index') }}">
            <i class="fas fa-procedures"></i>
            <span>Persalinan</span></a>
    </li>
    <li class="nav-item {{ Request::is('imunisasi*') ? 'active' : '' }}">
        <a class="nav-link " href="{{ route('imunisasi.index') }}">
            <i class="fas fa-baby"></i>
            <span>Imunisasi</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Datamaster
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item {{ Request::is('ibu*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('ibu.index') }}">
            <i class="fas fa-user-alt"></i>
            <span>Ibu</span></a>
    </li>
    <li class="nav-item {{ Request::is('anak*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('anak.index') }}">
            <i class="fas fa-user-alt"></i>
            <span>Anak</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('datauser.index') }}">
            <i class="fas fa-user"></i>
            <span>Data Akun</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    {{-- <!-- Heading -->
    <div class="sidebar-heading">
        Datamaster
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Obat</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Data Obat:</h6>
                <a class="collapse-item" href="buttons.html">Stok Obat</a>
                <a class="collapse-item" href="cards.html">Cards</a>
            </div>
        </div>
    </li> --}}

    {{-- <!-- Divider -->
    <hr class="sidebar-divider"> --}}

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Sidebar Message -->

</ul>
<!-- End of Sidebar -->
