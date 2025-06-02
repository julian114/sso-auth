<template>
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Estados de la República</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Utilerias</a></li>
                        <li class="breadcrumb-item"><a href="#">Catálogos</a></li>
                        <li class="breadcrumb-item" aria-current="page">Estados de la República</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Lista de Estados</h5>
                </div>

                <div class="card-body">

                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-start align-items-sm-center mb-3">
                        <div class="flex-grow-1" style="min-width: 200px;">
                            <input v-model="busqueda" type="text" class="form-control form-control-sm"
                                placeholder="Buscar..." />
                        </div>

                        <div>
                            <button class="btn btn-primary btn-sm w-100 w-sm-auto mt-2 mt-sm-0"
                                @click="nuevoEstado()">
                                <i class="fas fa-plus"></i> Nuevo Estado
                            </button>
                        </div>
                    </div>

                    <EasyDataTable
                            buttons-pagination
                            :headers="headers"
                            :items="estadosFiltrados"
                            :rows-per-page="10"
                            show-index
                            :sort-by="sortBy"
                            :sort-type="sortType"
                            multi-sort
                            theme-color="#8A8462"
                            table-class-name="customize-table"
                        >

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
                                    <a class="dropdown-item" href="#" @click.prevent="deleteEstado(id)">
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
                                    <VField name="nombre_modal" type="text" class="form-control" :class="{ 'is-invalid': errors.nombre_modal }" id="nombre_modal" v-model="nombre_modal" rules="required" />
                                    <label>Nombre del Estado</label>
                                    <VErrorMessage name="nombre_modal" class="text-danger mt-1 d-block small" />
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <VField name="abreviatura_modal" type="text" class="form-control" :class="{ 'is-invalid': errors.abreviatura_modal }" id="abreviatura_modal" v-model="abreviatura_modal" />
                                    <label>Abreviatura</label>
                                    <VErrorMessage name="abreviatura_modal" class="text-danger mt-1 d-block small" />
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <VField name="clave_modal" type="text" class="form-control" :class="{ 'is-invalid': errors.clave_modal }" id="clave_modal" v-model="clave_modal" />
                                    <label>Clave</label>
                                    <VErrorMessage name="clave_modal" class="text-danger mt-1 d-block small" />
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
            estados: [],
            flag: 0,
            modalTitle: "",
            idestado: "",
            nombre_modal: "",
            abreviatura_modal: "",
            clave_modal: "",
            headers: [
                { text: "NOMBRE", value: "nombre", sortable: true },
                { text: "ABREVIATURA", value: "abreviatura", sortable: true },
                { text: "CLAVE", value: "clave", sortable: true },
                { text: "OPCIONES", value: "opciones" }
            ],


        };
    },

    created() {
        this.get();
    },

    computed: {
        estadosFiltrados() {
            // Función para normalizar texto (quitar acentos y convertir a minúsculas)
            const normalizeText = (text) => {
                if (!text) return '';
                return text
                    .normalize('NFD')                           // Separa caracteres y diacríticos
                    .replace(/[\u0300-\u036f]/g, '')            // Elimina los diacríticos
                    .toLowerCase();                             // Convierte a minúsculas
            };

            const query = this.busqueda.trim();
            if (!query) return this.estados;

            const normalizedQuery = normalizeText(query);

            return this.estados.filter(item => {
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
                const response = await this.$axios.get('/Estados/get');
                if (response.data.status) {
                    this.estados = response.data.data;
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
                    abreviatura: this.abreviatura_modal,
                    clave: this.clave_modal,
                };

                const response = await this.$axios.post('/Estados/create', data);
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
                this.modalTitle = "Editar Estado";
                this.flag = 1;

                const response = await this.$axios.get(`/Estados/edit/${id}`);

                if (response.data.status) {
                    this.idestado = response.data.data.id;
                    this.nombre_modal = response.data.data.nombre;
                    this.abreviatura_modal = response.data.data.abreviatura;
                    this.clave_modal = response.data.data.clave;
                    this.abrirmodal();
                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al editar el estado.', 'error');
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
                    id: this.idestado,
                    nombre: this.nombre_modal,
                    abreviatura: this.abreviatura_modal,
                    clave: this.clave_modal,
                };

                const response = await this.$axios.put(`/Estados/update/${this.idestado}`, data);

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

        async deleteEstado(id) {

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

                    const response = await this.$axios.delete(`/Estados/delete/${id}`);

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
        /* Métodos para modal de Estados */
        nuevoEstado() {
            this.resetForm();
            this.modalTitle = "Nuevo Estado";
            this.flag = 0;
            this.abrirmodal();
        },

        resetForm() {
            this.$refs.form_ref.resetForm();
            this.nombre_modal = "";
            this.abreviatura_modal = "";
            this.clave_modal = "";
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
