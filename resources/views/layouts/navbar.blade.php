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

                @can('Modulo_Utilidades_Cursos')
                <li class="pc-item pc-caption">
                    <label>Cursos</label>
                    <i class="ti ti-dashboard"></i>
                </li>
                @endcan

                @can('Modulo_PlantillaConstancias')
                <li class="pc-item">
                    <a href="/Cursos" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-notebook"></i></span>
                        <span class="pc-mtext">Gestionar Cursos</span>
                    </a>
                </li>
                @endcan


                @can('Modulo_PlantillaConstancias')
                <li class="pc-item">
                    <a href="/PlantillasConstancias" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-book"></i></span>
                        <span class="pc-mtext">Plantillas de Constancias</span>
                    </a>
                </li>
                @endcan

                @can('Modulo_Participantes')
                <li class="pc-item">
                    <a href="/Participantes" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-users"></i></span>
                        <span class="pc-mtext">Participantes</span>
                    </a>
                </li>
                @endcan

                @can('Modulo_Ponentes')
                <li class="pc-item">
                    <a href="/Ponentes" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-users"></i></span>
                        <span class="pc-mtext">Ponentes</span>
                    </a>
                </li>
                @endcan

                @can('Modulo_Utilidades_Utileria')
                <li class="pc-item pc-caption">
                    <label>Utilerias</label>
                    <i class="ti ti-dashboard"></i>
                </li>
                @endcan

                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link"><span class="pc-micon"><i class="ti ti-menu"></i></span>
                        <span class="pc-mtext">Catálogos</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i>
                        </span>
                    </a>
                    <ul class="pc-submenu">
                        @can('Modulo_EstadosInscripcion')
                        <li class="pc-item">
                            <a class="pc-link" href="/EstadosInscripcion">Estados de Incripción</a>
                        </li>
                        @endcan
                        @can('Modulo_Institucion')
                        <li class="pc-item">
                            <a class="pc-link" href="/Institucion">Instituciones</a>
                        </li>
                        @endcan
                        @can('Modulo_Cursos')
                        <li class="pc-item">
                            <a class="pc-link" href="/MateriasCurso">Materias de Curso</a>
                        </li>
                        @endcan
                        @can('Modulo_ModalidadesCurso')
                        <li class="pc-item">
                            <a class="pc-link" href="/ModalidadesCurso">Modalidades de Curso</a>
                        </li>
                        @endcan
                        @can('Modulo_TiposCurso')
                        <li class="pc-item">
                            <a class="pc-link" href="/TiposCurso">Tipo de Curso</a>
                        </li>
                        @endcan
                        @can('Modulo_Estados')
                        <li class="pc-item">
                            <a class="pc-link" href="/Estados">Estados de la República</a>
                        </li>
                        @endcan
                        @can('Modulo_Municipios')
                        <li class="pc-item">
                            <a class="pc-link" href="/Municipios">Municipios</a>
                        </li>
                        @endcan
                    </ul>
                </li>

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
