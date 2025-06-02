<template>
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Usuarios</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Administración</a></li>
                        <li class="breadcrumb-item" aria-current="page">Usuarios</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Administración de Usuarios</h5>
                </div>

                <div class="card-body">

                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-start align-items-sm-center mb-3">
                        <div class="flex-grow-1" style="min-width: 200px;">
                            <input v-model="busqueda" type="text" class="form-control form-control-sm"
                                placeholder="Buscar..." />
                        </div>

                        <div>
                            <button v-if="hasPermission('Create_User')" class="btn btn-primary btn-sm w-100 w-sm-auto mt-2 mt-sm-0"
                                @click="nuevoUsuario()">
                                <i class="ti ti-user-plus"></i> Nuevo Usuario
                            </button>
                        </div>
                    </div>

                    <EasyDataTable
                            buttons-pagination
                            :headers="headersUsuarios"
                            :items="userFiltrados"
                            :rows-per-page="5"
                            show-index
                            :sort-by="sortByUsers"
                            :sort-type="sortTypeUsers"
                            multi-sort
                            theme-color="#8A8462"
                            table-class-name="customize-table"
                        >

                        <template #item-activo="{ activo }">
                            <span v-if="activo === 'Activo'" class="badge bg-light-success border border-success">
                               {{ activo }}
                            </span>
                            <span v-else class="badge bg-light-danger border border-danger">
                               {{ activo }}
                            </span>
                        </template>

                        <template #item-opciones="{ id }">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="#" @click.prevent="nuevoRolUser(id)">
                                        <i class="ti ti-select me-2 text-secondary"></i> Asignar rol
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" @click.prevent="edit(id)">
                                        <i class="ti ti-edit me-2 text-primary"></i> Editar
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" @click.prevent="updateStatus(id)">
                                        <i class="ti ti-exchange me-2 text-warning"></i> Cambiar estatus
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" @click.prevent="deleteU(id)">
                                        <i class="ti ti-trash me-2 text-danger"></i> Eliminar
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </template>

                        <template #empty-message>
                            <a>Sin información disponible</a>
                        </template>

                    </EasyDataTable>
                </div>

            </div>
        </div>

        <loader v-if="cargando" />
    </div>

    <!-- Modal -->
    <div id="modal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">{{ modalTitle }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <VForm ref="form_ref" v-slot="{ errors }" @submit="update|create">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-floating">
                                    <VField name="name_modal" type="text" class="form-control" :class="{ 'is-invalid': errors.name_modal }"
                                            placeholder="#" id="name_modal" v-model="name_modal" rules="required" />
                                    <label for="name_modal">Nombre Completo</label>
                                    <VErrorMessage name="name_modal" class="text-danger mt-1 d-block small" />
                                </div>

                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating">
                                    <VField name="email_modal" type="email" class="form-control" :class="{ 'is-invalid': errors.email_modal }"
                                            placeholder="#" id="email_modal" v-model="email_modal" rules="required|email" />
                                    <label for="email_modal">Correo Electrónico</label>
                                    <VErrorMessage name="email_modal" class="text-danger mt-1 d-block small" />
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="form-floating flex-grow-1">
                                        <VField name="password_modal" :type="mostrarPassword_modal ? 'text' : 'password'" class="form-control" :class="{ 'is-invalid': errors.password_modal }"
                                                placeholder="#" id="password_modal" v-model="password_modal" :rules="flag === 1 ? (password_modal ? 'strong_password' : '') : 'required|strong_password'" />
                                        <label for="password_modal">Contraseña</label>
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary" @click="togglePasswordVisibility_modal" style="height: 3.5rem; border-top-left-radius: 0; border-bottom-left-radius: 0;" aria-label="Mostrar u ocultar contraseña">
                                        <i :class="mostrarPassword_modal ? 'ti ti-eye' : 'ti ti-eye-off'"></i>
                                    </button>
                                </div>
                                <VErrorMessage name="password_modal" class="text-danger mt-1 d-block small"/>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button v-if="flag === 0" type="submit" class="btn btn-primary" @click="create">Guardar</button>
                            <button v-if="flag === 1" type="submit" class="btn btn-warning" @click="update">Actualizar</button>
                        </div>
                    </VForm>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin del modal -->

        <!-- Modal Permisos-->
    <div id="modalRoles" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">{{ modalTitle }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <input v-model="busquedaRoles" type="text" class="form-control form-control-sm w-50" placeholder="Buscar..."/>
                    </div>
                    <EasyDataTable
                        buttons-pagination
                        :headers="headersRoles"
                        :items="rolAsingadoFiltrados"
                        :rows-per-page="5"
                        show-index
                        :sort-by="sortByRoles"
                        :sort-type="sortTypeRoles"
                        multi-sort
                        table-class-name="customize-table"
                    >
                        <template #item-select="{ id }">
                        <div class="form-check">
                            <input
                            class="form-check-input"
                            type="radio"
                            :value="id"
                            v-model="rolAsignado"
                            name="rolSeleccionado"
                            />
                        </div>
                        </template>

                        <template #empty-message>
                            <a>Sin información disponible</a>
                        </template>
                    </EasyDataTable>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button class="btn btn-primary" @click="asignarRol">Asignar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin del modal -->

</template>

<script>
export default {

    components: {

    },

    props: {
        roleGranted: {
            type: Array,
            default: () => []
        },
        permissionsGranted: {
            type: Array,
            default: () => []
        },
    },

    data() {

        return {
            cargando: 0,
            busqueda: '',
            sortByUsers: [],
            sortTypeUsers: [],
            users: [],
            headersUsuarios: [
                { text: "NOMBRE", value: "name", sortable: true },
                { text: "CORREO ELECTRÓNICO", value: "email", sortable: true },
                { text: "ESTATUS", value: "activo", sortable: true },
                { text: "OPCIONES", value: "opciones" }
            ],
            flag: 0,
            modalTitle: "",
            iduser: "",
            name_modal: "",
            email_modal: "",
            password_modal: "",
            mostrarPassword_modal: false,

            busquedaRoles: '',
            sortByRoles: [],
            sortTypeRoles: [],
            roles: [],
            rolAsignado: [],
            headersRoles: [
                { text: 'NOMBRE DEL ROL', value: 'name', sortable: true },
                { text: 'DESCRIPCIÓN', value: 'descripcion', sortable: true, width: 500 },
                { text: 'SELECCIONAR', value: 'select', sortable: true },
            ],
        };
    },

    created() {
        this.get();
    },

    computed: {
        userFiltrados() {

            const normalizeText = (text) => {
                return text
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase();
            };

            const query = this.busqueda.trim();
            if (!query) return this.users;

            const normalizedQuery = normalizeText(query);

            return this.users.filter(item => {
                return Object.values(item).some(val => {
                    const normalizedValue = normalizeText(String(val));
                    return normalizedValue.includes(normalizedQuery);
                });
            });
        },

        rolAsingadoFiltrados() {

            const normalizeText = (text) => {
                return text
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase();
            };

            const query = this.busquedaRoles.trim();
            if (!query) return this.roles;

            const normalizedQuery = normalizeText(query);

            return this.roles.filter(item => {
                return Object.values(item).some(val => {
                    const normalizedValue = normalizeText(String(val));
                    return normalizedValue.includes(normalizedQuery);
                });
            });
        },
    },

    mounted() {
    },

    methods: {

        /* Método para gestionar permisos */
        hasPermission(permissionName) {
            return this.permissionsGranted.includes(permissionName);
        },

        /* Métodos generales */
        togglePasswordVisibility_modal() {
            this.mostrarPassword_modal = !this.mostrarPassword_modal;
        },

        /* Método para componente de carga */
        incrementarCarga() {
            this.cargando++;
        },

        decrementarCarga() {
            if (this.cargando > 0) {
                this.cargando--;
            }
        },

        /* Resto de Método */
        async get() {
            try {
                this.incrementarCarga();
                const response = await this.$axios.get('/Usuario/get');
                if (response.data.status) {
                    this.users = response.data.data;
                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }

            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al obtener los datos.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async create() {

            try {
                this.incrementarCarga();
                // Validación automática del formulario
                const { valid } = await this.$refs.form_ref.validate();

                if (!valid) {
                    this.$swal.fire('Error', 'Por favor complete los campos obligatorios correctamente', 'error');
                    this.decrementarCarga();
                    return;
                }

                const data = {
                    name: this.name_modal,
                    email: this.email_modal,
                    password: this.password_modal,
                };

                const response = await this.$axios.post('/Usuario/create', data);
                if (response.data.status) {
                    this.get();
                    this.cerrarmodal();
                    this.resetForm();
                    this.$swal.fire('Éxito', response.data.message, 'success');
                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async edit(id) {
            try {

                this.incrementarCarga();
                this.modalTitle = "Editar Datos del Usuario";
                this.flag = 1;
                this.password_modal = '';

                const response = await this.$axios.get(`/Usuario/edit/${id}`);

                if (response.data.status) {
                    this.iduser = response.data.data.id;
                    this.name_modal = response.data.data.name;
                    this.email_modal = response.data.data.email;
                    this.abrirmodal();
                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al editar la tarea.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async update() {

            try {

                this.incrementarCarga();

                // Validación automática del formulario
                const { valid } = await this.$refs.form_ref.validate();

                if (!valid) {
                    this.$swal.fire('Error', 'Por favor complete los campos obligatorios correctamente', 'error');
                    this.decrementarCarga();
                    return;
                }

                const data = {
                    id: this.iduser,
                    name: this.name_modal,
                    email: this.email_modal,
                    password: this.password_modal,
                };

                if (this.password_modal && this.password_modal.length > 0) {
                    data.password = this.password_modal;
                }

                const response = await this.$axios.put(`/Usuario/update/${this.iduser}`, data);

                if (response.data.status) {
                    this.get();
                    this.cerrarmodal();
                    this.resetForm();
                    this.$swal.fire('Éxito', response.data.message, 'success');
                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async updateStatus(id) {

            try {
                const confirmChange = await this.$swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Este registro cambiara su estatus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'Cancelar'
                });

                if (confirmChange.isConfirmed) {
                    this.incrementarCarga();

                    const response = await this.$axios.put(`/Usuario/updateStatus/${id}`);

                    if (response.data.status) {
                        this.$swal.fire('Éxito', response.data.message, 'success');
                        this.get();
                    } else {
                        this.$swal.fire('Error', response.data.message, 'error');
                    }
                }

            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al cambiar de estado.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async deleteU(id) {

            try {

                const confirmDelete = await this.$swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Este registro será eliminado permanentemente.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });

                if (confirmDelete.isConfirmed) {
                    this.incrementarCarga();

                    const response = await this.$axios.delete(`/Usuario/delete/${id}`);

                    if (response.data.status) {
                        this.$swal.fire('Éxito', response.data.message, 'success');
                        this.get();
                    } else {
                        this.$swal.fire('Error', response.data.message, 'error');
                    }
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al eliminar.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        /* Métodos para modal de Usuario */
        nuevoUsuario() {
            this.resetForm();
            this.modalTitle = "Nuevo Usuario";
            this.flag = 0;
            this.abrirmodal();
        },

        resetForm() {
            this.$refs.form_ref.resetForm();
            this.name_modal = "";
            this.email_modal = "";
            this.password_modal = "";
        },

        abrirmodal() {
            const modal = new bootstrap.Modal(document.getElementById('modal'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
            this.modalInstance = modal;
        },

        cerrarmodal() {
            if (this.modalInstance) {
                this.modalInstance.hide();
            }
        },



        /* Metodos para modal de permisos */
        async getSelectRole(id) {

            try {
                this.incrementarCarga();
                this.userSeleccionado = id;

                const response = await this.$axios.get(`/Usuario/selectRole/${id}`);

                if (response.data.status) {
                    this.roles = response.data.data.roles;
                    this.rolAsignado = response.data.data.rolAsignado[0] || null;

                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }

            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al obtener los datos.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async asignarRol() {
            try {
                this.incrementarCarga();

                const payload = {
                    user_id: this.userSeleccionado,
                    roles: this.rolAsignado,
                };

                const response = await this.$axios.post('/Usuario/asigRole', payload);

                if (response.data.status) {
                    this.get();
                    this.cerrarmodal();
                    this.resetFormRol();
                    this.$swal.fire('Éxito', response.data.message, 'success');
                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }

            } catch (error) {
                this.$swal.fire('Error', 'No se pudo asignar los permisos', 'error');
            } finally {
                this.decrementarCarga();
            }
        },

        nuevoRolUser($id) {
            this.resetFormRol();
            this.modalTitle = "Gestion de rol de Usuario | " + this.userFiltrados.find(userr => userr.id == $id).name;
            this.getSelectRole($id);
            this.abrirmodalRoles();
        },

        resetFormRol() {
            this.$refs.form_ref.resetForm();
            this.roles = [];
            this.rolAsignado = null;
            this.userSeleccionado = null;
        },

        abrirmodalRoles() {
            const modal = new bootstrap.Modal(document.getElementById('modalRoles'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
            this.modalInstance = modal;
        },


    }
};

</script>

<style>
.swal2-container {
    z-index: 11000 !important; /* Asegúrate de que SweetAlert2 esté por encima del modal */
}

.customize-table {
  --easy-table-header-font-size: 14px;
  --easy-table-header-height: 50px;
  --easy-table-header-item-padding: 10px 15px;
  --easy-table-body-row-height: 50px;
  --easy-table-body-row-font-size: 14px;
  --easy-table-body-item-padding: 10px 15px;
  --easy-table-footer-font-size: 14px;
  --easy-table-footer-padding: 0px 10px;
  --easy-table-footer-height: 50px;
  --easy-table-rows-per-page-selector-width: 70px;
  --easy-table-rows-per-page-selector-option-padding: 10px;
  --easy-table-rows-per-page-selector-z-index: 1;
}
/* Estilos globales para forzar que los menús desplegables aparezcan por encima de todo */
.dropdown-menu.show {
    z-index: 9999 !important;
}

/* Hacer que las celdas de la tabla permitan desbordamiento */
.vue3-easy-data-table__main,
.vue3-easy-data-table__body,
.vue3-easy-data-table__body tr,
.vue3-easy-data-table__body td {
    overflow: visible !important;
}
</style>
