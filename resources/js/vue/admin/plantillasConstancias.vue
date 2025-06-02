<template>
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Plantillas de Constancias</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Cursos</a></li>
                        <li class="breadcrumb-item" aria-current="page">Plantillas de Constancias</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Lista de Plantillas de Constancias</h5>
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <input v-model="busqueda" type="text" class="form-control form-control-sm"
                            placeholder="Buscar..." style="max-width: 250px;"/>

                        <button class="btn btn-primary btn-sm" @click="nuevaPlantilla()">
                            <i class="fas fa-plus"></i> Nueva Plantilla
                        </button>
                    </div>

                    <EasyDataTable
                            buttons-pagination
                            :headers="headers"
                            :items="plantillasFiltradas"
                            :rows-per-page="10"
                            show-index
                            :sort-by="sortBy"
                            :sort-type="sortType"
                            multi-sort
                            theme-color="#8A8462"
                            table-class-name="customize-table"
                        >

                        <template #item-tipo="{ tipo }">
                            <span v-if="tipo === 'participante'" class="badge bg-light-primary border border-primary">
                               Participante
                            </span>
                            <span v-else class="badge bg-light-info border border-info">
                               Ponente
                            </span>
                        </template>

                        <template #item-curso.nombre="{ curso }">
                            <span v-if="curso">{{ curso.nombre }}</span>
                            <span v-else class="text-muted">Sin curso asignado</span>
                        </template>

                        <template #item-activo="{ activo }">
                            <span v-if="activo" class="badge bg-light-success border border-success">
                               Activo
                            </span>
                            <span v-else class="badge bg-light-danger border border-danger">
                               Inactivo
                            </span>
                        </template>

                        <template #item-opciones="{ id }">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" @click.prevent="descargarPlantilla(id)">
                                    <i class="ti ti-download me-2 text-success"></i> Descargar
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
                                    <a class="dropdown-item" href="#" @click.prevent="deletePlantilla(id)">
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
                    <VForm ref="form_ref" v-slot="{ errors }" @submit="update|create" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-floating">
                                    <VField name="curso_id_modal" as="select" class="form-select" :class="{ 'is-invalid': errors.curso_id_modal }"
                                            id="curso_id_modal" v-model="curso_id_modal">
                                        <option value="" selected>Sin curso asignado</option>
                                        <option v-for="curso in cursos" :key="curso.id" :value="curso.id">
                                            {{ curso.nombre }}
                                        </option>
                                    </VField>
                                    <label for="curso_id_modal">Curso (opcional)</label>
                                    <VErrorMessage name="curso_id_modal" class="text-danger mt-1 d-block small" />
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating">
                                    <VField name="tipo_modal" as="select" class="form-select" :class="{ 'is-invalid': errors.tipo_modal }"
                                            id="tipo_modal" v-model="tipo_modal" rules="required">
                                        <option value="" selected disabled>Selecciona un tipo</option>
                                        <option value="participante">Participante</option>
                                        <option value="ponente">Ponente</option>
                                    </VField>
                                    <label for="tipo_modal">Tipo de Plantilla</label>
                                    <VErrorMessage name="tipo_modal" class="text-danger mt-1 d-block small" />
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-floating">
                                    <VField name="descripcion_modal" as="textarea" class="form-control" :class="{ 'is-invalid': errors.descripcion_modal }"
                                            placeholder="#" id="descripcion_modal" v-model="descripcion_modal" style="height: 100px" />
                                    <label for="descripcion_modal">Descripción (opcional)</label>
                                    <VErrorMessage name="descripcion_modal" class="text-danger mt-1 d-block small" />
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Archivo Word (.doc, .docx)</label>
                                <VField name="archivo" type="file" :rules="flag === 0 ? 'required' : ''" v-slot="{ handleChange, handleBlur, field, errors }">
                                    <input 
                                        type="file" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': errors.length > 0 }"
                                        @change="handleChange($event); handleFileChange($event)"
                                        @blur="handleBlur"
                                        accept=".doc,.docx"
                                        v-bind="field"
                                    />
                                    <div v-if="errors.length > 0" class="text-danger mt-1 d-block small">
                                        {{ errors[0] }}
                                    </div>
                                </VField>
                                <div class="form-text">
                                    <template v-if="flag === 1">
                                        El archivo actual es: <strong>{{ nombreArchivo }}</strong>. Sube un nuevo archivo solo si deseas reemplazar el actual.
                                    </template>
                                    <template v-else>
                                        Por favor, selecciona un archivo Word (.doc o .docx) para la plantilla.
                                    </template>
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
            plantillas: [],
            cursos: [],
            flag: 0,
            modalTitle: "",
            idplantilla: "",
            curso_id_modal: "",
            tipo_modal: "",
            descripcion_modal: "",
            archivo: null,
            nombreArchivo: "",

            headers: [
                { text: "TIPO", value: "tipo", sortable: true },
                { text: "CURSO", value: "curso.nombre", sortable: true },
                { text: "DESCRIPCIÓN", value: "descripcion", sortable: true },
                { text: "FECHA CREACIÓN", value: "created_at", sortable: true },
                { text: "ESTATUS", value: "activo", sortable: true },
                { text: "OPCIONES", value: "opciones" }
            ],


        };
    },

    created() {
        this.get();
    },

    computed: {
        plantillasFiltradas() {
            // Función para normalizar texto (quitar acentos y convertir a minúsculas)
            const normalizeText = (text) => {
                return text
                    .normalize('NFD')                           // Separa caracteres y diacríticos
                    .replace(/[\u0300-\u036f]/g, '')            // Elimina los diacríticos
                    .toLowerCase();                             // Convierte a minúsculas
            };

            const query = this.busqueda.trim();
            if (!query) return this.plantillas;

            const normalizedQuery = normalizeText(query);

            return this.plantillas.filter(item => {
                // Manejar valores anidados (como curso)
                let matchesQuery = false;
                
                // Comprobar tipo
                if (item.tipo && normalizeText(item.tipo).includes(normalizedQuery)) {
                    return true;
                }
                
                // Comprobar descripción
                if (item.descripcion && normalizeText(item.descripcion).includes(normalizedQuery)) {
                    return true;
                }
                
                // Comprobar curso
                if (item.curso && item.curso.nombre && normalizeText(item.curso.nombre).includes(normalizedQuery)) {
                    return true;
                }
                
                return matchesQuery;
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

        handleFileChange(event) {
            this.archivo = event.target.files[0];
        },

        /* Resto de Método */
        async get() {
            try {
                this.incrementarCarga();
                const response = await this.$axios.get('/PlantillasConstancias/get');
                if (response.data.status) {
                    this.plantillas = response.data.data;
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

        async getCursos() {
            try {
                this.incrementarCarga();
                const response = await this.$axios.get('/PlantillasConstancias/getCursos');
                if (response.data.status) {
                    this.cursos = response.data.data;
                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }

            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al obtener los cursos.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        /* Métodos para modal de Plantillas */
        nuevaPlantilla() {
            this.resetForm();
            this.getCursos();
            this.modalTitle = "Nueva Plantilla de Constancia";
            this.flag = 0;
            this.abrirmodal();
        },

        resetForm() {
            if (this.$refs.form_ref) {
                this.$refs.form_ref.resetForm();
            }
            this.curso_id_modal = "";
            this.tipo_modal = "";
            this.descripcion_modal = "";
            this.archivo = null;
            this.nombreArchivo = "";
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

        obtenerNombreArchivo(ruta) {
            if (!ruta) return '';
            const partes = ruta.split('/');
            return partes[partes.length - 1];
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

                // Crear un objeto FormData para enviar el archivo
                const formData = new FormData();
                formData.append('curso_id', this.curso_id_modal);
                formData.append('tipo', this.tipo_modal);
                formData.append('descripcion', this.descripcion_modal);
                formData.append('archivo', this.archivo);

                const response = await this.$axios.post('/PlantillasConstancias/create', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

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
                    this.$swal.fire('Error', 'Se produjo un error al crear la plantilla.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async edit(id) {
            try {
                this.incrementarCarga();
                this.getCursos();
                
                const response = await this.$axios.get(`/PlantillasConstancias/edit/${id}`);
                if (response.data.status) {
                    const plantilla = response.data.data;
                    this.idplantilla = plantilla.id;
                    this.curso_id_modal = plantilla.curso_id || "";
                    this.tipo_modal = plantilla.tipo;
                    this.descripcion_modal = plantilla.descripcion;
                    this.nombreArchivo = this.obtenerNombreArchivo(plantilla.ruta_archivo);
                    
                    this.flag = 1;
                    this.modalTitle = "Editar Plantilla de Constancia";
                    this.abrirmodal();
                } else {
                    this.$swal.fire('Error', response.data.message, 'error');
                }
            } catch (error) {
                if (error.response && error.response.data && error.response.data.message) {
                    this.$swal.fire('Error', error.response.data.message, 'error');
                } else {
                    this.$swal.fire('Error', 'Se produjo un error al obtener los datos de la plantilla.', 'error');
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

                // Crear un objeto FormData para enviar el archivo (si hay uno nuevo)
                const formData = new FormData();
                formData.append('curso_id', this.curso_id_modal);
                formData.append('tipo', this.tipo_modal);
                formData.append('descripcion', this.descripcion_modal);
                formData.append('_method', 'PUT'); // Para simular una solicitud PUT con FormData
                
                if (this.archivo) {
                    formData.append('archivo', this.archivo);
                }

                const response = await this.$axios.post(`/PlantillasConstancias/update/${this.idplantilla}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

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
                    this.$swal.fire('Error', 'Se produjo un error al actualizar la plantilla.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async updateStatus(id) {
            try {
                this.incrementarCarga();
                const response = await this.$axios.put(`/PlantillasConstancias/updateStatus/${id}`);
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
                    this.$swal.fire('Error', 'Se produjo un error al cambiar el estado de la plantilla.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async deletePlantilla(id) {
            try {
                const result = await this.$swal.fire({
                    title: '¿Estás seguro?',
                    text: '¡No podrás revertir esto! Se eliminará tanto el registro como el archivo físico.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '¡Sí, eliminarlo!',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    this.incrementarCarga();
                    const response = await this.$axios.delete(`/PlantillasConstancias/delete/${id}`);
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
                    this.$swal.fire('Error', 'Se produjo un error al eliminar la plantilla.', 'error');
                }
            } finally {
                this.decrementarCarga();
            }
        },

        async descargarPlantilla(id) {
            try {
                this.incrementarCarga();
                window.location.href = `/PlantillasConstancias/download/${id}`;
            } catch (error) {
                this.$swal.fire('Error', 'Se produjo un error al descargar el archivo.', 'error');
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