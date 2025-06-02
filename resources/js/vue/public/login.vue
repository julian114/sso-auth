<template>
    <div class="card my-5">
        <div class="card-body">
            <div class="text-center d-flex justify-content-between align-items-end mb-4">
                 <h2 class="mb-3 text-secondary" >SSO-AUTH</h2>
            </div>

            <div class="d-flex justify-content-between align-items-end mb-4">
                <h3 class="mb-0"><b>Iniciar Sesión</b></h3>
            </div>

            <VForm @submit="ingresar" v-slot="{ errors }" ref="form_login">

                <div class="form-floating mb-4">
                    <VField name="email" type="email" class="form-control" :class="{ 'is-invalid': errors.email }" id="email"
                            placeholder="@" v-model="email" rules="required|email" />
                    <label for="email">Correo Electrónico</label>
                    <VErrorMessage name="email" class="text-danger mt-1 d-block small" />
                </div>

                <div class="col-md-12 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="form-floating flex-grow-1">
                            <VField name="password" :type="mostrarPassword ? 'text' : 'password'" class="form-control" :class="{ 'is-invalid': errors.password }"
                                    placeholder="********" v-model="password" rules="required" />
                            <label for="password">Contraseña</label>
                        </div>
                        <button type="button" class="btn btn-outline-secondary" @click="togglePasswordVisibility" style="height: 3.5rem; border-top-left-radius: 0; border-bottom-left-radius: 0;" aria-label="Mostrar u ocultar contraseña">
                            <i :class="mostrarPassword ? 'ti ti-eye' : 'ti ti-eye-off'"></i>
                        </button>
                    </div>
                    <VErrorMessage name="password" class="text-danger mt-1 d-block small"/>
                </div>

                <div class="d-flex mt-1 justify-content-between">
                    <div class="form-check">
                        <input class="form-check-input input-primary" v-model="rememberMe" type="checkbox" id="customCheckc1"/>
                        <label class="form-check-label text-muted" for="customCheckc1">Recordar</label>
                    </div>
                    <h5 class="text-secondary f-w-400">¿Olvidaste tu contraseña?</h5>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Iniciar Sesión</button>
                </div>
                
                <div class="d-flex align-items-center my-4">
                    <hr class="flex-grow-1">
                    <span class="px-3 text-muted">o</span>
                    <hr class="flex-grow-1">
                </div>
                
                <div class="d-grid">
                    <a href="/login/microsoft" class="btn btn-outline-secondary btn-lg d-flex align-items-center justify-content-center">
                        <i class="ti ti-brand-microsoft me-2" style="font-size: 1.2rem;"></i>
                        Iniciar sesión con Microsoft 365
                    </a>
                </div>
            </VForm>

        </div>
    </div>

    <loader v-if="cargandoCount > 0" />

</template>

<script>
export default {

    components: {

    },

    data() {
        return {
            cargandoCount: 0,
            email: "",
            password: "",
            mostrarPassword: false,
            rememberMe: false,
        };

    },

    computed: {

    },

    mounted() {
        this.cargarEmailRecordado();
    },

    methods: {

        /* Métodos generales */
        togglePasswordVisibility_modal() {
            this.mostrarPassword = !this.mostrarPassword;
        },

        /* Método para componente de carga */
        incrementarCarga() {
            this.cargandoCount++;
        },

        decrementarCarga() {
            if (this.cargandoCount > 0) {
                this.cargandoCount--;
            }
        },

        togglePasswordVisibility() {
            this.mostrarPassword = !this.mostrarPassword;
        },

        async ingresar() {
            try {
                this.incrementarCarga();

                const { valid } = await this.$refs.form_login.validate();
                if (!valid) {
                    this.$swal.fire('Error', 'Por favor complete los campos obligatorios correctamente', 'error');
                    this.decrementarCarga();
                    return;
                }

                const data = {
                    email: this.email,
                    password: this.password,
                };

                const response = await this.$axios.post('/login', data);

                if (response.data.status) {

                    if (this.rememberMe) {
                        localStorage.setItem('recordarEmail', this.email);
                    } else {
                        localStorage.removeItem('recordarEmail');
                    }
                    window.location.href = response.data.redirect;
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

        cargarEmailRecordado() {
            const recordarEmail = localStorage.getItem('recordarEmail');
            if (recordarEmail) {
                this.email = recordarEmail;
                this.rememberMe = true;
            }
        },

        resetForm(){
            this.name = "",
            this.email = ""
        }
    },

};
</script>
