<ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
    @foreach ($navMenu as $rowNavMenu)
        <li class="{{ ($rowNavMenu['menuType'] === 'parent') ? 'nav-group' : 'nav-item' }}">
            <a class="nav-link {{ array_key_exists($rowNavMenu['menuId'], $navSubmenu) ? 'nav-group-toggle' : '' }}" href="{{
                ($rowNavMenu['menuType'] === 'child' || $rowNavMenu['menuType'] === 'url')
                    ? route($rowNavMenu['routeName'])
                    : 'javascript:void(0);'
            }}">
                <i class="{{ $rowNavMenu['menuIcon'] }} nav-icon"></i>
                {{ $rowNavMenu['menuName'] }}
            </a>

            @if (array_key_exists($rowNavMenu['menuId'], $navSubmenu))
                @foreach ($navSubmenu[$rowNavMenu['menuId']] as $rowSubmenu)
                    <ul class="nav-group-items compact">
                        <li class="nav-item">
                            <a class="nav-link" href="{{
                                ($rowSubmenu['menuType'] === 'child' || $rowSubmenu['menuType'] === 'url')
                                    ? route($rowSubmenu['routeName'])
                                    : 'javascript:void(0);'
                            }}">
                                {{ $rowSubmenu['submenuName'] }}
                            </a>
                        </li>
                    </ul>
                @endforeach
            @endif
        </li>
    @endforeach
</ul>