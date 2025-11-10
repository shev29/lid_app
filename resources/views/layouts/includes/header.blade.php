<header class="header header-sticky p-0 mb-3">
    <div class="container-fluid border-bottom px-3">
        <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()" style="margin-inline-start: -12px;">
            <i class="fa-solid fa-bars icon icon-menu icon-md"></i>
        </button>
        <ul class="header-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link notificationHeader" href="javascript:void(0);" title="Notifications"><i class="fa-regular fa-bell icon icon-md"></i></a>
            </li>
        </ul>
        <ul class="header-nav">
            {{-- <li class="nav-item dropdown">
                <button class="btn btn-link nav-link py-2 px-2 d-flex align-items-center" type="button"
                    aria-expanded="false" data-coreui-toggle="dropdown">
                    <svg class="icon icon-lg theme-icon-active">
                        <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-contrast"></use>
                    </svg>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="--cui-dropdown-min-width: 8rem;">
                    <li>
                        <button class="dropdown-item d-flex align-items-center" type="button"
                            data-coreui-theme-value="light">
                            <svg class="icon icon-lg me-3">
                                <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-sun"></use>
                            </svg>Light
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item d-flex align-items-center" type="button"
                            data-coreui-theme-value="dark">
                            <svg class="icon icon-lg me-3">
                                <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-moon"></use>
                            </svg>Dark
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item d-flex align-items-center active" type="button"
                            data-coreui-theme-value="auto">
                            <svg class="icon icon-lg me-3">
                                <use xlink:href="node_modules/@coreui/icons/sprites/free.svg#cil-contrast">
                                </use>
                            </svg>Auto
                        </button>
                    </li>
                </ul>
            </li>
            <li class="nav-item py-1">
                <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li> --}}
            <li class="nav-item dropdown">
                <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar avatar-md">
                        <img class="avatar-img" src="{{ $avatarUrl }}" alt="">
                    </div>
                    <span class="d-none d-md-inline avatar-name">{{ $employeeName }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item fw-medium p-md-2-2" id="listProfile" href="javascript:void(0);" data-token="{{ $employeeIdEncrypt }}"><i class="fa-regular fa-user icon me-2 fw-medium"></i>Profile</a>
                    <a class="dropdown-item fw-medium p-md-2-2" id="listChangePassword" href="javascript:void(0);" data-token="{{ $employeeIdEncrypt }}"><i class="fa-regular fa-unlock icon me-2 fw-medium"></i>Change Password</a>
                    <div class="dropdown-divider"></div>                   
                    <a class="dropdown-item fw-medium p-md-2-2" href="{{ route('logout') }}"><i class="fa-solid fa-arrow-right-from-bracket icon me-2 fw-medium"></i>Logout</a>
                </div>
            </li>
        </ul>
    </div>
    {{-- <div class="container-fluid breadcrumb-container px-3">
        @yield('breadcrumb')
    </div> --}}
</header>