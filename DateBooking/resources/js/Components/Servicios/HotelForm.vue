<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-white p-6">
        <div class="container bg-white rounded-2xl shadow-2xl overflow-hidden max-w-4xl mx-auto">
            <div class="formulario transform-gpu">
                <h1 class="titulo text-3xl font-bold text-gray-900 relative">
                    Crear Servicio de Hotel
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-gradient-to-r from-blue-600 to-blue-700">
                    </div>
                </h1>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Información básica -->
                    <div class="formulario-filas">
                        <div class="formulario-grupo">
                            <label class="text-gray-700 font-semibold">Nombre del Hotel</label>
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
                            <label class="text-gray-700 font-semibold">Costo por noche</label>
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

                        <div class="border p-6 rounded-lg mb-4 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="formulario-grupo">
                                    <label class="text-gray-700 font-semibold">Hora de Check-in</label>
                                    <input type="text" v-model="formData.disponibilidad[0].hora_inicio" required
                                        placeholder="HH:mm" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                                        class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        @input="validarHora($event, 'inicio', 0)" />
                                    <p class="text-sm text-gray-500 mt-1">Formato: HH:mm (24 horas)</p>
                                </div>

                                <div class="formulario-grupo">
                                    <label class="text-gray-700 font-semibold">Hora de Check-out</label>
                                    <input type="text" v-model="formData.disponibilidad[0].hora_fin" required
                                        placeholder="HH:mm" pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                                        class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        @input="validarHora($event, 'fin', 0)" />
                                    <p class="text-sm text-gray-500 mt-1">Formato: HH:mm (24 horas)</p>
                                </div>

                                <div class="formulario-grupo">
                                    <label class="text-gray-700 font-semibold">Intervalo entre reservas</label>
                                    <select v-model="formData.disponibilidad[0].intervalo" required
                                        class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                        <option value="">Seleccione el intervalo</option>
                                        <option value="12:00:00">12 horas</option>
                                        <option value="24:00:00">24 horas</option>
                                        <option value="23:59:59">1 día completo</option>
                                    </select>
                                    <p class="text-sm text-gray-500 mt-1">Tiempo asignado para cada reserva</p>
                                </div>

                                <div class="formulario-grupo">
                                    <label class="text-gray-700 font-semibold">Tipo de Disponibilidad</label>
                                    <select v-model="formData.disponibilidad[0].tipo" required
                                        class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                        <option value="recurrente">Recurrente</option>
                                        <option value="unico">Único</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Habitaciones -->
                    <div class="mt-8 border-t pt-8">
                        <h2 class="titulo text-2xl font-bold text-gray-900 relative mb-6">
                            Habitaciones
                            <div
                                class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-gradient-to-r from-blue-600 to-blue-700">
                            </div>
                        </h2>

                        <div v-if="formData.habitacion.length === 0" class="border p-6 rounded-lg mb-4 bg-gray-50">
                            <div class="w-full flex flex-col items-center">
                                <div class="w-full grid grid-cols-2 gap-4 p-4">
                                    <div class="formulario-grupo">
                                        <label class="text-gray-700 font-semibold">Tipo</label>
                                        <select v-model="nuevaHabitacion.tipo"
                                            class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent w-full">
                                            <option value="">Seleccione tipo</option>
                                            <option value="Individual">Individual</option>
                                            <option value="Doble">Doble</option>
                                            <option value="Suite">Suite</option>
                                            <option value="Familiar">Familiar</option>
                                        </select>
                                    </div>

                                    <div class="formulario-grupo">
                                        <label class="text-gray-700 font-semibold">Número Inicial</label>
                                        <input type="number" v-model="nuevaHabitacion.numeroInicial" min="1"
                                            class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent w-full" />
                                    </div>

                                    <div class="formulario-grupo">
                                        <label class="text-gray-700 font-semibold">Capacidad</label>
                                        <input type="number" v-model="nuevaHabitacion.capacidad" min="1"
                                            class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent w-full" />
                                    </div>

                                    <div class="formulario-grupo">
                                        <label class="text-gray-700 font-semibold">Cantidad</label>
                                        <input type="number" v-model="nuevaHabitacion.cantidad" min="1"
                                            class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent w-full" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de habitaciones agregadas -->
                        <div v-if="formData.habitacion.length > 0" class="mt-6">
                            <h3 class="text-lg font-semibold mb-4">Habitaciones registradas</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div v-for="(habitacion, index) in formData.habitacion" :key="index"
                                    class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-semibold">{{ habitacion.tipo }}</p>
                                            <p class="text-sm text-gray-600">Número: {{ habitacion.numero }}</p>
                                            <p class="text-sm text-gray-600">Capacidad: {{ habitacion.capacidad }}
                                                personas</p>
                                        </div>
                                        <button type="button" @click="eliminarHabitacion(index)"
                                            class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
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

const router = useRouter()
const emit = defineEmits(['submit', 'cancel'])
const ciudades = ref([])
const previewUrl = ref(null)
const loading = ref(false)

const nuevaHabitacion = ref({
    tipo: '',
    numeroInicial: 1,
    capacidad: 1,
    cantidad: 1
})

const formData = ref({
    nombre: '',
    descripcion: '',
    costo: '',
    categoria: 'hotel',
    id_ciudad: '',
    imagen: null,
    disponibilidad: [
        {
            hora_inicio: '',
            hora_fin: '',
            intervalo: '',
            tipo: 'recurrente',
            activo: 1
        }
    ],
    habitacion: []
})

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

const eliminarHabitacion = (index) => {
    formData.value.habitacion.splice(index, 1);
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
                } else if (tipo === 'fin') {
                    formData.value.disponibilidad[index].hora_fin = horaFormateada;
                } else if (tipo === 'intervalo') {
                    formData.value.disponibilidad[index].intervalo = horaFormateada;
                }
            }
        }
    }
}

const handleImageUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        formData.value.imagen = file
        previewUrl.value = URL.createObjectURL(file)
    }
}

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

        // Verificar que se hayan ingresado los datos de la habitación
        if (!nuevaHabitacion.value.tipo || !nuevaHabitacion.value.numeroInicial || !nuevaHabitacion.value.capacidad || !nuevaHabitacion.value.cantidad) {
            alert('Por favor complete todos los datos de la habitación');
            loading.value = false;
            return;
        }

        // Preparar las habitaciones para enviar
        const habitaciones = [];
        const numeroInicial = parseInt(nuevaHabitacion.value.numeroInicial);
        const cantidad = parseInt(nuevaHabitacion.value.cantidad);
        const tipo = nuevaHabitacion.value.tipo;
        const capacidad = parseInt(nuevaHabitacion.value.capacidad);

        // Generar las habitaciones
        for (let i = 0; i < cantidad; i++) {
            habitaciones.push({
                tipo: tipo,
                numero: numeroInicial + i,
                capacidad: capacidad
            });
        }

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
        formDataToSend.append('disponibilidad', JSON.stringify(formData.value.disponibilidad));
        formDataToSend.append('habitacion', JSON.stringify(habitaciones));

        const response = await axios.post('/api/servicios/nuevo-hotel', formDataToSend, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.status === 201) {
            alert('Hotel creado exitosamente');
            emit('submit', response.data);
            router.push('/servicio-agregados');
        }
    } catch (error) {
        console.error('Error al crear el hotel:', error);
        alert('Error al crear el hotel: ' + (error.response?.data?.error || error.message));
    } finally {
        loading.value = false;
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
