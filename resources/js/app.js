import './bootstrap';
import { createApp } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import loader from './components/loader.vue';
import EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';

// Importar vee-validate
import { Form, Field, ErrorMessage, defineRule, configure } from 'vee-validate';
import { required, email, numeric, url, alpha_dash, min, max, min_value, max_value, confirmed } from '@vee-validate/rules';
import { localize, setLocale } from '@vee-validate/i18n';

// Configurar vee-validate globalmente
defineRule('required', required);
defineRule('email', email);
defineRule('numeric', numeric);
defineRule('url', url);
defineRule('alpha_dash', alpha_dash);
defineRule('min', min);
defineRule('max', max);
defineRule('min_value', min_value);
defineRule('max_value', max_value);
defineRule('confirmed', confirmed);
defineRule('strong_password', value => {
  if (!value) return false;
  if (value.length < 8) return false;
  if (!/[a-z]/.test(value)) return false;
  if (!/[A-Z]/.test(value)) return false;
  if (!/\d/.test(value)) return false;
  return true;
});




// Configurar mensajes de error en español
configure({
    generateMessage: localize({
        es: {
            messages: {
                required: 'Este campo es obligatorio',
                email: 'Correo electrónico inválido',
                numeric: 'Este campo debe ser un número',
                url: 'URL inválida',
                alpha_dash: 'Este campo solo puede contener caracteres alfanuméricos, guiones y guiones bajos',
                min: 'Este campo debe tener al menos {length} caracteres',
                max: 'Este campo no puede tener más de {length} caracteres',
                min_value: 'El valor mínimo permitido es {min}',
                max_value: 'El valor máximo permitido es {max}',
                confirmed: 'Las contraseñas no coinciden',
                strong_password: 'La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas y números'
            }
        }
    }),
    validateOnInput: true
});

// Establece el idioma predeterminado
setLocale('es');

const app = createApp({});

// Registrar componentes globales de vee-validate
app.component('VForm', Form);
app.component('VField', Field);
app.component('VErrorMessage', ErrorMessage);

app.config.globalProperties.$axios = axios;
app.config.globalProperties.$swal = Swal;
app.component('loader', loader);
app.component('EasyDataTable', EasyDataTable);

/* contenido para las rutas*/

import Login from './vue/public/login.vue';
app.component('login-vue', Login);

import Dashboard from './vue/admin/dashboard.vue';
app.component('dashboard-vue', Dashboard);

import EstadoIncripcion from './vue/admin/estadoInscripcion.vue';
app.component('estado-inscripcion-vue', EstadoIncripcion);

import Institucion from './vue/admin/institucion.vue';
app.component('institucion-vue', Institucion);

import User from './vue/admin/user.vue';
app.component('user-vue', User);

import Role from './vue/admin/role.vue';
app.component('role-vue', Role);

import Permiso from './vue/admin/permissions.vue';
app.component('permiso-vue', Permiso);

import MateriasCurso from './vue/admin/materiasCurso.vue';
app.component('materias-curso-vue', MateriasCurso);

import ModalidadesCurso from './vue/admin/modalidadesCurso.vue';
app.component('modalidades-curso-vue', ModalidadesCurso);

import TiposCurso from './vue/admin/tiposCurso.vue';
app.component('tipos-curso-vue', TiposCurso);

import Estados from './vue/admin/estados.vue';
app.component('estados-vue', Estados);

import Municipios from './vue/admin/municipios.vue';
app.component('municipios-vue', Municipios);

import Participantes from './vue/admin/participantes.vue';
app.component('participantes-vue', Participantes);

import Ponentes from './vue/admin/ponentes.vue';
app.component('ponentes-vue', Ponentes);

import PlantillasConstancias from './vue/admin/plantillasConstancias.vue';
app.component('plantillas-constancias-vue', PlantillasConstancias);

app.mount('#app');
