<template>
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Participantes</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Cursos</a></li>
                        <li class="breadcrumb-item" aria-current="page">Participantes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Lista de Participantes</h5>
                </div>

                <div class="card-body">

                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-start align-items-sm-center mb-3">

                        <div class="flex-grow-1" style="min-width: 200px;">
                            <input v-model="busqueda" type="text" class="form-control form-control-sm"
                                placeholder="Buscar..." />
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-2 mt-sm-0">
                            <button class="btn btn-success btn-sm" @click="actualizarDesdeApi()">
                                <i class="fas fa-sync"></i> Actualizar desde API
                            </button>
                            <button class="btn btn-primary btn-sm" @click="nuevoParticipante()">
                                <i class="fas fa-plus"></i> Nuevo Participante
                            </button>
                        </div>
                    </div>


                    <EasyDataTable
                            buttons-pagination
                            :headers="headers"
                            :items="participantesFiltrados"
                            :rows-per-page="10"
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

                        <template #item-es_api="{ es_api }">
                            <span v-if="es_api" class="badge bg-light-primary border border-primary">
                               API
                            </span>
                            <span v-else class="badge bg-light-secondary border border-secondary">
                               Manual
                            </span>
                        </template>

                        <template #item-opciones="{ id, es_api }">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                <li v-if="!es_api">
                                    <a class="dropdown-item" href="#" @click.prevent="edit(id)">
                                    <i class="ti ti-edit me-2 text-primary"></i> Editar
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" @click.prevent="updateStatus(id)">
                                    <i class="ti ti-exchange me-2 text-warning"></i> Cambiar estatus
                                    </a>
                                </li>
                                <li v-if="!es_api">
                                    <a class="dropdown-item" href="#" @click.prevent="deleteParticipante(id)">
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
                                    <VField name="nombre_modal" type="text" class="form-control" :class="{ 'is-invalid': errors.nombre_modal }"
                                            placeholder="#" id="nombre_modal" v-model="nombre_modal" rules="required" />
                                    <label>Nombre</label>
                                    <VErrorMessage name="nombre_modal" class="text-danger mt-1 d-block small" />
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <VField name="apellido_paterno_modal" type="text" class="form-control" :class="{ 'is-invalid': errors.apellido_paterno_modal }"
                                            placeholder="#" id="apellido_paterno_modal" v-model="apellido_paterno_modal" rules="required" />
                                    <label>Apellido Paterno</label>
                                    <VErrorMessage name="apellido_paterno_modal" class="text-danger mt-1 d-block small" />
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <VField name="apellido_materno_modal" type="text" class="form-control" :class="{ 'is-invalid': errors.apellido_materno_modal }"
                                            placeholder="#" id="apellido_materno_modal" v-model="apellido_materno_modal" />
                                    <label>Apellido Materno</label>
                                    <VErrorMessage name="apellido_materno_modal" class="text-danger mt-1 d-block small" />
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <VField name="grado_academico_modal" type="text" class="form-control" :class="{ 'is-invalid': errors.grado_academico_modal }"
                                            placeholder="#" id="grado_academico_modal" v-model="grado_academico_modal" rules="required"/>
                                    <label>Grado Académico</label>
                                    <VErrorMessage name="grado_academico_modal" class="text-danger mt-1 d-block small" />
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <VField name="no_empleado_modal" type="text" class="form-control" :class="{ 'is-invalid': errors.no_empleado_modal }"
                                            placeholder="#" id="no_empleado_modal" v-model="no_empleado_modal" rules="required"/>
                                    <label>No. Empleado</label>
                                    <VErrorMessage name="no_empleado_modal" class="text-danger mt-1 d-block small" />
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating">
                                    <VField name="institucion_id_modal" as="select" class="form-select" :class="{ 'is-invalid': errors.institucion_id_modal }"
                                            id="institucion_id_modal" v-model="institucion_id_modal" rules="required">
                                        <option value="" selected disabled>Selecciona una institución</option>
                                        <option v-for="institucion in instituciones" :key="institucion.id" :value="institucion.id">
                                            {{ institucion.nombre }} ({{ institucion.siglas }})
                                        </option>
                                    </VField>
                                    <label for="institucion_id_modal">Institución</label>
                                    <VErrorMessage name="institucion_id_modal" class="text-danger mt-1 d-block small" />
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
            participantes: [],
            instituciones: [],
            flag: 0,
            modalTitle: "",
            idparticipante: "",
            nombre_modal: "",
            apellido_paterno_modal: "",
            apellido_materno_modal: "",
            grado_academico_modal: "",
            no_empleado_modal: "",
            institucion_id_modal: "",

            headers: [
                { text: "NOMBRE COMPLETO", value: "nombre_completo", sortable: true },
                { text: "GRADO ACADÉMICO", value: "grado_academico", sortable: true },
                { text: "NO. EMPLEADO", value: "no_empleado", sortable: true },
                { text: "INSTITUCIÓN", value: "institucion.nombre", sortable: true },
                { text: "ORIGEN", value: "es_api", sortable: true },
                { text: "ESTATUS", value: "activo", sortable: true },
                { text: "OPCIONES", value: "opciones" }
            ],


        };
    },

    created() {
        this.get();
    },

    computed: {
        participantesFiltrados() {
            // Función para normalizar texto (quitar acentos y convertir a minúsculas)
            const normalizeText = (text) => {
                return text
                    .normalize('NFD')                           // Separa caracteres y diacríticos
                    .replace(/[\u0300-\u036f]/g, '')            // Elimina los diacríticos
                    .toLowerCase();                             // Convierte a minúsculas
            };

            const query = this.busqueda.trim();
            if (!query) return this.participantes;

            const normalizedQuery = normalizeText(query);

            return this.participantes.filter(item => {
                return Object.values(item).some(val => {
                    // Convierte el valor a string, normaliza y busca coincidencia
                    if (val === null || val === undefined) return false;
                    if (typeof val === 'object') {
                        // Para manejar relaciones anidadas como institucion
                        return Object.values(val).some(nestedVal => {
                            if (nestedVal === null || nestedVal === undefined) return false;
                            const normalizedValue = normalizeText(String(nestedVal));
                            return normalizedValue.includes(normalizedQuery);
                        });
                    }
                    const normalizedValue = normalizeText(String(val));
                    return normalizedValue.includes(normalizedQuery);
                });
            });
        }
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
                const response = await this.$axios.get('/Participantes/get');
                if (response.data.status) {
                    this.participantes = response.data.data.map(item => {
                        return {
                            ...item,
                            activo: item.activo ? 'Activo' : 'Inactivo'
                        };
                    });
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

        async getInstituciones() {
            try {
                this.incrementarCarga();
                const response = await this.$axios.get('/Participantes/getInstituciones');
                if (response.data.status) {
                    this.instituciones = response.data.data;
                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }

            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al obtener las instituciones.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        /* Métodos para modal de Participantes */
        nuevoParticipante() {
            this.resetForm();
            this.getInstituciones();
            this.modalTitle = "Nuevo Participante";
            this.flag = 0;
            this.abrirmodal();
        },

        resetForm() {
            if (this.$refs.form_ref) {
                this.$refs.form_ref.resetForm();
            }
            this.nombre_modal = "";
            this.apellido_paterno_modal = "";
            this.apellido_materno_modal = "";
            this.grado_academico_modal = "";
            this.no_empleado_modal = "";
            this.institucion_id_modal = "";
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
                    apellido_paterno: this.apellido_paterno_modal,
                    apellido_materno: this.apellido_materno_modal,
                    grado_academico: this.grado_academico_modal,
                    no_empleado: this.no_empleado_modal,
                    institucion_id: this.institucion_id_modal
                };

                const response = await this.$axios.post('/Participantes/create', data);
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
                    this.$swal.fire('Error', 'Se produjo un error al crear el participante.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async edit(id) {
            try {
                this.incrementarCarga();
                this.getInstituciones();

                const response = await this.$axios.get(`/Participantes/edit/${id}`);
                if (response.data.status) {
                    const participante = response.data.data;
                    this.idparticipante = participante.id;
                    this.nombre_modal = participante.nombre;
                    this.apellido_paterno_modal = participante.apellido_paterno;
                    this.apellido_materno_modal = participante.apellido_materno;
                    this.grado_academico_modal = participante.grado_academico;
                    this.no_empleado_modal = participante.no_empleado;
                    this.institucion_id_modal = participante.institucion_id;

                    this.flag = 1;
                    this.modalTitle = "Editar Participante";
                    this.abrirmodal();
                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al obtener los datos del participante.', 'error');
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
                    nombre: this.nombre_modal,
                    apellido_paterno: this.apellido_paterno_modal,
                    apellido_materno: this.apellido_materno_modal,
                    grado_academico: this.grado_academico_modal,
                    no_empleado: this.no_empleado_modal,
                    institucion_id: this.institucion_id_modal
                };

                const response = await this.$axios.put(`/Participantes/update/${this.idparticipante}`, data);
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
                    this.$swal.fire('Error', 'Se produjo un error al actualizar el participante.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async updateStatus(id) {
            try {
                this.incrementarCarga();
                const response = await this.$axios.put(`/Participantes/updateStatus/${id}`);
                if (response.data.status) {
                    this.get();
                    this.$swal.fire('Éxito', response.data.message, 'success');
                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al cambiar el estado del participante.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async deleteParticipante(id) {
            try {
                const result = await this.$swal.fire({
                    title: '¿Estás seguro?',
                    text: '¡No podrás revertir esto!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '¡Sí, eliminarlo!',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    this.incrementarCarga();
                    const response = await this.$axios.delete(`/Participantes/delete/${id}`);
                    if (response.data.status) {
                        this.get();
                        this.$swal.fire('Eliminado', response.data.message, 'success');
                    } else {
                        this.$swal.fire('Error', response.data.message, 'error');
                    }
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al eliminar el participante.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async actualizarDesdeApi() {
            try {
                this.incrementarCarga();
                const response = await this.$axios.post('/Participantes/actualizarDesdeApi');
                if (response.data.status) {
                    this.get();
                    this.$swal.fire('Éxito', response.data.message, 'success');
                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al actualizar los participantes desde la API.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        }
    }
}
</script>
<style>
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
