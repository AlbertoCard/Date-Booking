<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-white p-6">
        <Loader :visible="loading" />
        <div class="container bg-white rounded-2xl shadow-2xl overflow-hidden max-w-4xl mx-auto">
            <div class="formulario transform-gpu">
                <h1 class="titulo text-3xl font-bold text-gray-900 relative">
                    Crear Servicio de Evento
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-gradient-to-r from-blue-600 to-blue-700">
                    </div>
                </h1>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Información básica -->
                    <div class="formulario-filas">
                        <div class="formulario-grupo">
                            <label class="text-gray-700 font-semibold">Nombre del Evento</label>
                            <input type="text" v-model="formData.nombre" required
                                class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent" />
                        </div>

                        <div class="formulario-grupo">
                            <label class="text-gray-700 font-semibold">Ciudad</label>
                            <select v-model="formData.id_ciudad" required
                                class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                <option value="">Selecciona una ciudad</option>
                                <option v-for="ciudad in ciudades" :key="ciudad.id_ciudad" :value="ciudad.id_ciudad">
                                    {{ ciudad.nombre }}
                                </option>
                            </select>
                        </div>

                        <div class="formulario-grupo">
                            <label class="text-gray-700 font-semibold">Costo por entrada</label>
                            <input type="number" v-model="formData.costo" required min="0" step="0.01"
                                class="precio-input transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                placeholder="0.00" />
                        </div>
                    </div>

                    <div class="formulario-grupo">
                        <label class="text-gray-700 font-semibold">Descripción</label>
                        <textarea v-model="formData.descripcion" required
                            class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent min-h-[120px]"></textarea>
                    </div>

                    <div class="formulario-grupo">
                        <label class="text-gray-700 font-semibold">Imagen del servicio (opcional)</label>
                        <div class="col-span-2">
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg v-if="!formData.imagen" class="mx-auto h-12 w-12 text-gray-400"
                                        stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <img v-else :src="previewUrl" class="mx-auto h-32 w-32 object-cover rounded-lg">
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Subir una imagen</span>
                                            <input id="file-upload" name="file-upload" type="file" class="sr-only"
                                                @change="handleImageUpload" accept="image/*">
                                        </label>
                                        <p class="pl-1">o arrastrar y soltar</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Disponibilidad -->
                    <div class="mt-8 border-t pt-8">
                        <h2 class="titulo text-2xl font-bold text-gray-900 relative mb-6">
                            Disponibilidad
                            <div
                                class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-gradient-to-r from-blue-600 to-blue-700">
                            </div>
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="formulario-grupo">
                                <label class="text-gray-700 font-semibold">Fecha del Evento</label>
                                <input type="date" v-model="formData.disponibilidad[0].fecha" required
                                    class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent" />
                            </div>

                            <div class="formulario-grupo">
                                <label class="text-gray-700 font-semibold">Hora de inicio</label>
                                <input type="text" v-model="formData.disponibilidad[0].hora_inicio" required
                                    placeholder="HH:mm" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                                    class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                    @input="validarHora($event, 'inicio', 0)" />
                                <p class="text-sm text-gray-500 mt-1">Formato: HH:mm (24 horas)</p>
                            </div>

                            <div class="formulario-grupo">
                                <label class="text-gray-700 font-semibold">Hora de fin</label>
                                <input type="text" v-model="formData.disponibilidad[0].hora_fin" required
                                    placeholder="HH:mm" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                                    class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                    @input="validarHora($event, 'fin', 0)" />
                                <p class="text-sm text-gray-500 mt-1">Formato: HH:mm (24 horas)</p>
                            </div>

                            <div class="formulario-grupo">
                                <label class="text-gray-700 font-semibold">Intervalo</label>
                                <input type="text" v-model="formData.disponibilidad[0].intervalo" required
                                    placeholder="00:00:00"
                                    class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent" />
                            </div>

                            <div class="formulario-grupo">
                                <label class="text-gray-700 font-semibold">Tipo de Disponibilidad</label>
                                <select v-model="formData.disponibilidad[0].tipo" required
                                    class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                    <option value="unica">Única</option>
                                    <option value="recurrente">Recurrente</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Lugares -->
                    <div class="mt-8 border-t pt-8">
                        <h2 class="titulo text-2xl font-bold text-gray-900 relative mb-6">
                            Configuración de Lugares
                            <div
                                class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-gradient-to-r from-blue-600 to-blue-700">
                            </div>
                        </h2>

                        <div class="border p-6 rounded-lg mb-4 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="formulario-grupo">
                                    <label class="text-gray-700 font-semibold">Número de Filas</label>
                                    <input type="number" v-model="formData.lugares[0].filas" required min="1"
                                        class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent" />
                                </div>

                                <div class="formulario-grupo">
                                    <label class="text-gray-700 font-semibold">Número de Asientos por Fila</label>
                                    <input type="number" v-model="formData.lugares[0].numeros" required min="1"
                                        class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" @click="router.back()"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all duration-300">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transform hover:scale-105 transition-all duration-300">
                            Crear Servicio
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import Loader from '../Loader.vue'

const router = useRouter()
const emit = defineEmits(['submit', 'cancel'])
const ciudades = ref([])
const previewUrl = ref(null)
const loading = ref(false)

const formData = ref({
    nombre: '',
    descripcion: '',
    costo: '',
    categoria: 'evento',
    id_ciudad: '',
    imagen: null,
    disponibilidad: [
        {
            fecha: '',
            hora_inicio: '',
            hora_fin: '',
            intervalo: '00:00:00',
            tipo: 'unica',
            activo: 1
        }
    ],
    lugares: [
        {
            filas: '',
            numeros: ''
        }
    ]
})

const formatTime = (time) => {
    if (!time) return '';
    // Asegurarse de que la hora tenga el formato HH:mm
    const [hours, minutes] = time.split(':');
    return `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
}

const cargarCiudades = async () => {
    try {
        const response = await axios.get('/api/ciudades')
        ciudades.value = response.data
    } catch (error) {
        console.error('Error al cargar ciudades:', error)
    }
}

onMounted(() => {
    cargarCiudades()
})

const handleSubmit = async () => {
    loading.value = true;
    try {
        const userData = JSON.parse(localStorage.getItem('userData'));
        if (!userData || userData.rol !== 'establecimiento') {
            alert('Error: Debes ser un establecimiento para crear servicios');
            loading.value = false;
            return;
        }
        const estabResponse = await axios.get(`/api/establecimientos/usuario/${userData.uid}`);
        if (!estabResponse.data.establecimientos || estabResponse.data.establecimientos.length === 0) {
            alert('Error: No se encontró el establecimiento');
            loading.value = false;
            return;
        }
        const idEstablecimiento = estabResponse.data.establecimientos[0].id_establecimiento;

        // Crear FormData
        const formDataToSend = new FormData();
        formDataToSend.append('id_establecimiento', idEstablecimiento);
        formDataToSend.append('nombre', formData.value.nombre);
        formDataToSend.append('descripcion', formData.value.descripcion);
        formDataToSend.append('costo', formData.value.costo);
        formDataToSend.append('categoria', formData.value.categoria);
        formDataToSend.append('id_ciudad', formData.value.id_ciudad);
        if (formData.value.imagen) {
            formDataToSend.append('imagen', formData.value.imagen);
        }
        formDataToSend.append('disponibilidad', JSON.stringify([{
            fecha: formData.value.disponibilidad[0].fecha,
            hora_inicio: formatTime(formData.value.disponibilidad[0].hora_inicio) + ':00',
            hora_fin: formatTime(formData.value.disponibilidad[0].hora_fin) + ':00',
            intervalo: '00:00:00',
            tipo: formData.value.disponibilidad[0].tipo,
            activo: 1
        }]));
        formDataToSend.append('lugares', JSON.stringify([{
            filas: parseInt(formData.value.lugares[0].filas),
            numeros: parseInt(formData.value.lugares[0].numeros)
        }]));

        const response = await axios.post('/api/servicios/nuevo-evento', formDataToSend, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.status === 201) {
            emit('submit', response.data);
            router.push('/servicio-agregados');
        }
    } catch (error) {
        console.error('Error al enviar el formulario:', formData.value);
        console.error('Error al crear el evento:', error);
    } finally {
        loading.value = false;
    }
}

const handleImageUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        formData.value.imagen = file
        previewUrl.value = URL.createObjectURL(file)
    }
}

const validarHora = (event, tipo, index) => {
    const valor = event.target.value;
    const regex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;

    if (valor && !regex.test(valor)) {
        // Si el valor no coincide con el formato, intentamos formatearlo
        const numeros = valor.replace(/[^0-9]/g, '');
        if (numeros.length >= 4) {
            const horas = numeros.slice(0, 2);
            const minutos = numeros.slice(2, 4);

            if (parseInt(horas) <= 23 && parseInt(minutos) <= 59) {
                const horaFormateada = `${horas}:${minutos}`;
                if (tipo === 'inicio') {
                    formData.value.disponibilidad[index].hora_inicio = horaFormateada;
                } else {
                    formData.value.disponibilidad[index].hora_fin = horaFormateada;
                }
            }
        }
    }
}
</script>

<style scoped>
.container {
    backdrop-filter: blur(10px);
    transform-style: preserve-3d;
    perspective: 1000px;
}

.formulario {
    padding: 3rem;
    max-width: 100%;
    width: 100%;
    margin: auto;
}

.formulario-filas {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    margin-top: 2rem;
}

.formulario-grupo {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 250px;
}

.formulario-grupo label {
    margin-bottom: 0.5rem;
    color: #1f2937;
    font-weight: 600;
}

.formulario-grupo input,
.formulario-grupo select,
.formulario-grupo textarea {
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    border: 2px solid #e5e7eb;
    font-size: 0.875rem;
    width: 100%;
    background: #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.formulario-grupo input:hover,
.formulario-grupo select:hover,
.formulario-grupo textarea:hover {
    border-color: #93c5fd;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.formulario-grupo input:focus,
.formulario-grupo select:focus,
.formulario-grupo textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

textarea {
    resize: vertical;
    min-height: 120px;
}

.precio-input {
    color: #2563eb;
    font-weight: 600;
    background-color: #f8fafc;
}

.titulo {
    text-align: center;
    padding-bottom: 1rem;
    margin-bottom: 2rem;
    position: relative;
    color: #1f2937;
}

@media (max-width: 768px) {
    .formulario {
        padding: 1.5rem;
    }

    .formulario-filas {
        flex-direction: column;
        gap: 1rem;
    }

    .formulario-grupo {
        width: 100%;
    }
}
</style>