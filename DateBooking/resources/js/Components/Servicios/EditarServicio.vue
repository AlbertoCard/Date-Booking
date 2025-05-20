<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-white p-6">
        <div class="container bg-white rounded-2xl shadow-2xl overflow-hidden max-w-2xl mx-auto p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Editar Servicio</h1>

            <!-- Mensajes de estado -->
            <div v-if="mensaje.texto"
                :class="['mb-4 p-4 rounded-lg', mensaje.tipo === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700']">
                {{ mensaje.texto }}
            </div>

            <form @submit.prevent="guardarDisponibilidad" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fecha -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de inicio</label>
                        <input type="date" v-model="disponibilidad.fecha" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p v-if="errores.fecha" class="mt-1 text-sm text-red-600">{{ errores.fecha }}</p>
                    </div>

                    <!-- Intervalo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Intervalo</label>
                        <input type="time" v-model="disponibilidad.intervalo" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Formato: HH:mm</p>
                    </div>

                    <!-- Hora Inicio -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hora de inicio</label>
                        <input type="time" v-model="disponibilidad.hora_inicio" required step="1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p v-if="errores.hora_inicio" class="mt-1 text-sm text-red-600">{{ errores.hora_inicio }}</p>
                    </div>

                    <!-- Hora Fin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hora de fin</label>
                        <input type="time" v-model="disponibilidad.hora_fin" required step="1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p v-if="errores.hora_fin" class="mt-1 text-sm text-red-600">{{ errores.hora_fin }}</p>
                    </div>

                    <!-- Días -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Días disponibles</label>
                        <div class="flex flex-wrap gap-4">
                            <label v-for="dia in diasSemana" :key="dia.valor" class="inline-flex items-center">
                                <input type="checkbox" v-model="diasSeleccionados" :value="dia.valor"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2">{{ dia.nombre }}</span>
                            </label>
                        </div>
                        <p v-if="errores.dias" class="mt-2 text-sm text-red-600">{{ errores.dias }}</p>
                    </div>

                    <!-- Tipo de Disponibilidad -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Tipo de disponibilidad</label>
                        <select v-model="disponibilidad.tipo" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="unico">Unico</option>
                            <option value="recurrente">Recurrente</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" @click="$router.push('/servicio-agregados')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="!formularioValido || guardando" :class="['px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white',
                        formularioValido && !guardando
                            ? 'bg-blue-600 hover:bg-blue-700'
                            : 'bg-blue-300 cursor-not-allowed']">
                        {{ guardando ? 'Guardando...' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'EditarServicio',
    data() {
        return {
            disponibilidad: {
                id_servicio: this.$route.params.id,
                fecha: '',
                hora_inicio: '',
                hora_fin: '',
                intervalo: '00:30',
                dias: '',
                tipo: 'regular'
            },
            diasSemana: [
                { nombre: 'Lunes', valor: 'lunes' },
                { nombre: 'Martes', valor: 'martes' },
                { nombre: 'Miércoles', valor: 'miercoles' },
                { nombre: 'Jueves', valor: 'jueves' },
                { nombre: 'Viernes', valor: 'viernes' },
                { nombre: 'Sábado', valor: 'sabado' },
                { nombre: 'Domingo', valor: 'domingo' }
            ],
            diasSeleccionados: [],
            guardando: false,
            mensaje: {
                texto: '',
                tipo: ''
            },
            errores: {
                fecha: '',
                hora_inicio: '',
                hora_fin: '',
                dias: ''
            }
        };
    },
    computed: {
        hayDiasSeleccionados() {
            return this.diasSeleccionados.length > 0;
        },
        formularioValido() {
            return this.disponibilidad.fecha &&
                this.disponibilidad.hora_inicio &&
                this.disponibilidad.hora_fin &&
                this.disponibilidad.intervalo &&
                this.hayDiasSeleccionados &&
                this.horasValidas;
        },
        horasValidas() {
            if (!this.disponibilidad.hora_inicio || !this.disponibilidad.hora_fin) return true;
            return this.disponibilidad.hora_inicio < this.disponibilidad.hora_fin;
        }
    },
    async mounted() {
        await this.cargarDisponibilidad();
    },
    methods: {
        async cargarDisponibilidad() {
            try {
                const response = await axios.get(`/api/servicios/${this.$route.params.id}`);
                const servicio = response.data;

                if (servicio.disponibilidad && servicio.disponibilidad.length > 0) {
                    const disp = servicio.disponibilidad[0];
                    this.disponibilidad = {
                        id_servicio: this.$route.params.id,
                        fecha: disp.fecha,
                        hora_inicio: disp.hora_inicio,
                        hora_fin: disp.hora_fin,
                        intervalo: disp.intervalo,
                        tipo: disp.tipo
                    };
                    this.diasSeleccionados = [disp.dias];
                }
            } catch (error) {
                console.error('Error al cargar la disponibilidad:', error);
                this.mensaje = {
                    texto: 'Error al cargar la disponibilidad',
                    tipo: 'error'
                };
            }
        },
        formatearHora(hora) {
            if (!hora) return '';
            if (hora.split(':').length === 3) return hora;
            return hora + ':00';
        },
        validarFormulario() {
            this.errores = {
                fecha: '',
                hora_inicio: '',
                hora_fin: '',
                dias: ''
            };

            let esValido = true;

            // Validar fecha
            if (!this.disponibilidad.fecha) {
                this.errores.fecha = 'La fecha es requerida';
                esValido = false;
            } else {
                const fechaSeleccionada = new Date(this.disponibilidad.fecha);
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0);
                fechaSeleccionada.setHours(0, 0, 0, 0);
                if (fechaSeleccionada < hoy) {
                    this.errores.fecha = 'La fecha no puede ser anterior a hoy';
                    esValido = false;
                }
            }

            // Validar horas
            if (!this.disponibilidad.hora_inicio) {
                this.errores.hora_inicio = 'La hora de inicio es requerida';
                esValido = false;
            }
            if (!this.disponibilidad.hora_fin) {
                this.errores.hora_fin = 'La hora de fin es requerida';
                esValido = false;
            }
            if (!this.horasValidas) {
                this.errores.hora_inicio = 'La hora de inicio debe ser menor a la hora de fin';
                this.errores.hora_fin = 'La hora de fin debe ser mayor a la hora de inicio';
                esValido = false;
            }

            // Validar días seleccionados
            if (!this.hayDiasSeleccionados) {
                this.errores.dias = 'Debe seleccionar al menos un día';
                esValido = false;
            }

            return esValido;
        },
        async guardarDisponibilidad() {
            if (!this.validarFormulario()) {
                this.mensaje = {
                    texto: 'Por favor, corrija los errores en el formulario',
                    tipo: 'error'
                };
                return;
            }

            this.guardando = true;
            this.mensaje = { texto: '', tipo: '' };

            try {
                // Primero, verificar que tenemos un ID de servicio válido
                if (!this.disponibilidad.id_servicio) {
                    throw new Error('ID de servicio no válido');
                }

                // Primero eliminar las disponibilidades existentes
                await axios.delete(`/api/disponibilidad/${this.disponibilidad.id_servicio}`);

                // Crear un registro por cada día seleccionado
                const promesasDisponibilidad = this.diasSeleccionados.map(async (dia) => {
                    const datosAEnviar = {
                        id_servicio: parseInt(this.disponibilidad.id_servicio),
                        fecha: this.disponibilidad.fecha,
                        hora_inicio: this.formatearHora(this.disponibilidad.hora_inicio),
                        hora_fin: this.formatearHora(this.disponibilidad.hora_fin),
                        intervalo: this.formatearHora(this.disponibilidad.intervalo),
                        dias: dia,
                        tipo: this.disponibilidad.tipo
                    };

                    console.log('Enviando datos:', datosAEnviar);
                    return axios.post('/api/disponibilidad', datosAEnviar);
                });

                await Promise.all(promesasDisponibilidad);

                this.mensaje = {
                    texto: 'Disponibilidad actualizada exitosamente',
                    tipo: 'exito'
                };

                setTimeout(() => {
                    this.$router.push('/servicio-agregados');
                }, 1500);
            } catch (error) {
                console.error('Error completo:', error);
                this.mensaje = {
                    texto: error.response?.data?.message || 'Error al actualizar la disponibilidad',
                    tipo: 'error'
                };
            } finally {
                this.guardando = false;
            }
        }
    }
};
</script>

<style scoped>
.container {
    backdrop-filter: blur(10px);
    transform-style: preserve-3d;
    perspective: 1000px;
}
</style>
