<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-white p-6">
        <div class="container bg-white rounded-2xl shadow-2xl overflow-hidden max-w-2xl mx-auto p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Agregar Disponibilidad</h1>

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
                    </div>

                    <!-- Hora Fin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hora de fin</label>
                        <input type="time" v-model="disponibilidad.hora_fin" required step="1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                        <p v-if="!hayDiasSeleccionados" class="mt-2 text-sm text-red-600">
                            Selecciona al menos un día
                        </p>
                    </div>

                    <!-- Tipo de Disponibilidad -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Tipo de disponibilidad</label>
                        <select v-model="disponibilidad.tipo" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="regular">Regular</option>
                            <option value="especial">Especial</option>
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
import API_ROUTES from '../../utils/index.js';

export default {
    name: 'NuevaDisponibilidad',
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
    methods: {
        formatearHora(hora) {
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
                if (fechaSeleccionada < hoy) {
                    this.errores.fecha = 'La fecha no puede ser anterior a hoy';
                    esValido = false;
                }
            }

            // Validar horas
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
                const promesasDisponibilidad = this.diasSeleccionados.map(async (dia) => {
                    const datosAEnviar = {
                        ...this.disponibilidad,
                        hora_inicio: this.formatearHora(this.disponibilidad.hora_inicio),
                        hora_fin: this.formatearHora(this.disponibilidad.hora_fin),
                        intervalo: this.formatearHora(this.disponibilidad.intervalo),
                        dias: dia,
                        fecha: this.disponibilidad.fecha
                    };

                    return axios.post(API_ROUTES.disponibilidad.crear, datosAEnviar);
                });

                await Promise.all(promesasDisponibilidad);

                this.mensaje = {
                    texto: 'Disponibilidad guardada exitosamente',
                    tipo: 'exito'
                };

                setTimeout(() => {
                    this.$router.push('/servicio-agregados');
                }, 1500);
            } catch (error) {
                console.error('Error completo:', error);
                this.mensaje = {
                    texto: error.response?.data?.message || 'Error al guardar la disponibilidad',
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
