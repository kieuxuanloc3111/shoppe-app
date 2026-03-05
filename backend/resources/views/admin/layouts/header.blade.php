<header class="topbar" data-navbarbg="skin6">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">
        <div class="navbar-header" data-logobg="skin5">

            <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
                <i class="ti-menu ti-close"></i>
            </a>

            <div class="navbar-brand">
                <a href="#" class="logo">
                    <b class="logo-icon">
                        <img src="{{ asset('admin/assets/images/logo-icon.png') }}" class="dark-logo"/>
                        <img src="{{ asset('admin/assets/images/logo-light-icon.png') }}" class="light-logo"/>
                    </b>
                    <span class="logo-text">
                        <img src="{{ asset('admin/assets/images/logo-text.png') }}" class="dark-logo"/>
                        <img src="{{ asset('admin/assets/images/logo-light-text.png') }}" class="light-logo"/>
                    </span>
                </a>
            </div>

            <a class="topbartoggler d-block d-md-none waves-effect waves-light"
               href="javascript:void(0)"
               data-toggle="collapse"
               data-target="#navbarSupportedContent">
                <i class="ti-more"></i>
            </a>
        </div>

        <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin6">

            <ul class="navbar-nav float-left mr-auto">
                <li class="nav-item search-box">
                    <a class="nav-link waves-effect waves-dark" href="javascript:void(0)">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-magnify font-20 mr-1"></i>
                            <div class="ml-1 d-none d-sm-block">
                                <span>Search</span>
                            </div>
                        </div>
                    </a>
                    <form class="app-search position-absolute">
                        <input type="text" class="form-control" placeholder="Search &amp; enter">
                        <a class="srh-btn"><i class="ti-close"></i></a>
                    </form>
                </li>
            </ul>

            <ul class="navbar-nav float-right">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic"
                       data-toggle="dropdown">
                        <img src="{{ asset('admin/assets/images/users/1.jpg') }}"
                             class="rounded-circle" width="31">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right user-dd animated">
                        <a class="dropdown-item" href="#"><i class="ti-user"></i> My Profile</a>
                    <a class="dropdown-item"
                    href="#"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ti-power-off m-r-5 m-l-5"></i> Logout
                    </a>

                    <form id="logout-form"
                        action="{{ route('logout') }}"
                        method="POST"
                        style="display: none;">
                        @csrf
                    </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
