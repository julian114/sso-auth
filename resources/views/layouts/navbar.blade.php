<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="/Dashboard" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="../assets/images/logo/logo.png" class="img-fluid logo-lg" style="width: 45px; height: 45px;" alt="logo">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                @can('Modulo_Dashboard')
                <li class="pc-item">
                    <a href="/Dashboard" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>
                @endcan

                @can('Modulo_Utilidades_Administracion')
                <li class="pc-item pc-caption">
                    <label>Administración</label>
                    <i class="ti ti-dashboard"></i>
                </li>
                @endcan

                @can('Modulo_Usuario')
                <li class="pc-item">
                    <a href="/Usuario" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-users"></i></span>
                        <span class="pc-mtext">Usuarios</span>
                    </a>
                </li>
                @endcan

                @can('Modulo_Rol')
                <li class="pc-item">
                    <a href="/Rol" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-chart-candle"></i></span>
                        <span class="pc-mtext">Roles</span>
                    </a>
                </li>
                @endcan

                @can('Modulo_Permiso')
                <li class="pc-item">
                    <a href="/Permiso" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-lock-access"></i></span>
                        <span class="pc-mtext">Permisos</span>
                    </a>
                </li>
                @endcan

                @can('Modulo_Log')
                <li class="pc-item">
                    <a href="/Logs" target="_blank" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-flask"></i></span>
                        <span class="pc-mtext">Logs</span>
                    </a>
                </li>
                @endcan
            </ul>
        </div>
    </div>
</nav>
