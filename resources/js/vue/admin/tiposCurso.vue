<template>
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Tipo de Curso</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Utilerias</a></li>
                        <li class="breadcrumb-item"><a href="#">Catálogos</a></li>
                        <li class="breadcrumb-item" aria-current="page">Tipo de Curso</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Lista de Tipo de Curso</h5>
                </div>

                <div class="card-body">

                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-start align-items-sm-center mb-3">
                        <div class="flex-grow-1" style="min-width: 200px;">
                            <input v-model="busqueda" type="text" class="form-control form-control-sm"
                                placeholder="Buscar..." />
                        </div>

                        <div>
                            <button class="btn btn-primary btn-sm w-100 w-sm-auto mt-2 mt-sm-0"
                                @click="nuevoTipo()">
                                <i class="fas fa-plus"></i> Nuevo Tipo de Curso
                            </button>
                        </div>
                    </div>

                    <EasyDataTable
                            buttons-pagination
                            :headers="headers"
                            :items="TiposFiltradas"
                            :rows-per-page="5"
                            show-index
                            :sort-by="sortBy"
                            :sort-type="sortType"
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
                                    <a class="dropdown-item" href="#" @click.prevent="deleteTipo(id)">
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
                                    <VField name="nombre_modal" type="text" class="form-control" :class="{ 'is-invalid': errors.nombre_modal }" id="nombre_modal" v-model="nombre_modal" rules="required" placeholder="Nombre del Tipo de Curso" />
                                    <label>Nombre del Tipo de Curso</label>
                                    <VErrorMessage name="nombre_modal" class="text-danger mt-1 d-block small" />
                                </div>

                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating">
                                    <VField name="descripcion_modal" type="text" class="form-control" :class="{ 'is-invalid': errors.descripcion_modal }" id="descripcion_modal" v-model="descripcion_modal" rules="required" as="textarea" style="height:100px;" placeholder="Descripción del Tipo de Curso" />
                                    <label>Descripción del Tipo de Curso</label>
                                    <VErrorMessage name="descripcion_modal" class="text-danger mt-1 d-block small" />
                                </div>
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

</template>

<script>
export default {

    components: {

    },

    data() {

        return {
            cargando: 0,
            busqueda: '',
            sortBy: [],
            sortType: [],
            tipos: [],
            flag: 0,
            modalTitle: "",
            idTipo: "",
            nombre_modal: "",
            descripcion_modal: "",
            headers: [
                { text: "NOMBRE", value: "nombre", sortable: true },
                { text: "DESCRIPCIÓN", value: "descripcion", sortable: true },
                { text: "ESTATUS", value: "activo", sortable: true },
                { text: "OPCIONES", value: "opciones" }
            ],


        };
    },

    created() {
        this.get();
    },

    computed: {
        TiposFiltradas() {
            // Función para normalizar texto (quitar acentos y convertir a minúsculas)
            const normalizeText = (text) => {
                return text
                    .normalize('NFD')                           // Separa caracteres y diacríticos
                    .replace(/[\u0300-\u036f]/g, '')            // Elimina los diacríticos
                    .toLowerCase();                             // Convierte a minúsculas
            };

            const query = this.busqueda.trim();
            if (!query) return this.tipos;

            const normalizedQuery = normalizeText(query);
            return this.tipos.filter(item => {
                return Object.values(item).some(val => {
                    // Convierte el valor a string, normaliza y busca coincidencia
                    const normalizedValue = normalizeText(String(val));
                    return normalizedValue.includes(normalizedQuery);
                });
            });
        }

    },

    mounted() {
    },

    methods: {

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
                const response = await this.$axios.get('/TiposCurso/get');
                if (response.data.status) {
                    this.tipos = response.data.data;
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
                    nombre: this.nombre_modal,
                    descripcion: this.descripcion_modal,
                };

                const response = await this.$axios.post('/TiposCurso/create', data);
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
                this.modalTitle = "Editar Tipo de Curso";
                this.flag = 1;

                const response = await this.$axios.get(`/TiposCurso/edit/${id}`);

                if (response.data.status) {
                    this.idtipo = response.data.data.id;
                    this.nombre_modal = response.data.data.nombre;
                    this.descripcion_modal = response.data.data.descripcion;
                    this.abrirmodal();
                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al editar el tipo de curso.', 'error');
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
                    id: this.idtipo,
                    nombre: this.nombre_modal,
                    descripcion: this.descripcion_modal,
                };

                const response = await this.$axios.put(`/TiposCurso/update/${this.idtipo}`, data);

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

                    const response = await this.$axios.put(`/TiposCurso/updateStatus/${id}`);

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

        async deleteTipo(id) {

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

                    const response = await this.$axios.delete(`/TiposCurso/delete/${id}`);

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
        /* Métodos para modal de Tipos de Curso */
        nuevoTipo() {
            this.resetForm();
            this.modalTitle = "Nuevo Tipo de Curso";
            this.flag = 0;
            this.abrirmodal();
        },

        resetForm() {
            this.$refs.form_ref.resetForm();
            this.nombre_modal = "";
            this.descripcion_modal = "";
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
