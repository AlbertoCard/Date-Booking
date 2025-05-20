<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-white p-6">
        <div class="container bg-white rounded-2xl shadow-2xl overflow-hidden max-w-4xl mx-auto">
            <div class="formulario transform-gpu">
                <h1 class="titulo text-3xl font-bold text-gray-900 relative">
                    Agregar nuevo servicio
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-gradient-to-r from-blue-600 to-blue-700">
                    </div>
                </h1>

                <form @submit.prevent="guardarServicio" class="space-y-6">
                    <div class="formulario-filas">
                        <div class="formulario-grupo">
                            <label class="text-gray-700 font-semibold">Nombre del servicio</label>
                            <input type="text" v-model="servicio.nombre" required
                                class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent" />
                        </div>
                        <div class="formulario-grupo">
                            <label class="text-gray-700 font-semibold">Categoría</label>
                            <input type="text" v-model="servicio.categoria" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="formulario-grupo">
                            <label class="text-gray-700 font-semibold">Ciudad</label>
                            <select v-model="servicio.id_ciudad" required
                                class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                <option value="">Selecciona una ciudad</option>
                                <option v-for="ciudad in ciudades" :key="ciudad.id_ciudad" :value="ciudad.id_ciudad">
                                    {{ ciudad.nombre }}
                                </option>
                            </select>
                            <p v-if="formErrors.id_ciudad" class="text-red-500 text-sm mt-1">{{ formErrors.id_ciudad }}
                            </p>
                        </div>
                    </div>

                    <div class="formulario-grupo">
                        <label class="text-gray-700 font-semibold">Descripción del servicio</label>
                        <textarea v-model="servicio.descripcion" required
                            class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent min-h-[120px]"></textarea>
                    </div>

                    <div class="formulario-grupo">
                        <label class="text-gray-700 font-semibold">Precio</label>
                        <input type="number" v-model="servicio.costo" required min="0" step="0.01"
                            class="precio-input transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                            placeholder="0.00" />
                    </div>

                    <div class="formulario-grupo">
                        <label class="text-gray-700 font-semibold">Imagen del servicio (opcional)</label>
                        <div class="col-span-2">
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg v-if="!servicio.imagen" class="mx-auto h-12 w-12 text-gray-400"
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
                        <h1 class="titulo text-3xl font-bold text-gray-900 relative">
                            Disponibilidad del servicio
                            <div
                                class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-gradient-to-r from-blue-600 to-blue-700">
                            </div>
                        </h1>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Fecha -->
                            <div class="formulario-grupo">
                                <label class="text-gray-700 font-semibold">Fecha de inicio</label>
                                <input type="date" v-model="disponibilidad.fecha" required
                                    class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                <p v-if="formErrors.fecha" class="text-red-500 text-sm mt-1">{{ formErrors.fecha }}</p>
                            </div>

                            <!-- Intervalo -->
                            <div class="formulario-grupo">
                                <label class="text-gray-700 font-semibold">Intervalo</label>
                                <input type="time" v-model="disponibilidad.intervalo" required
                                    class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                <p class="text-sm text-gray-500 mt-1">Formato: HH:mm</p>
                            </div>

                            <!-- Hora Inicio -->
                            <div class="formulario-grupo">
                                <label class="text-gray-700 font-semibold">Hora de inicio</label>
                                <input type="time" v-model="disponibilidad.hora_inicio" required step="1"
                                    class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                <p v-if="formErrors.hora_inicio" class="text-red-500 text-sm mt-1">{{
                                    formErrors.hora_inicio }}</p>
                            </div>

                            <!-- Hora Fin -->
                            <div class="formulario-grupo">
                                <label class="text-gray-700 font-semibold">Hora de fin</label>
                                <input type="time" v-model="disponibilidad.hora_fin" required step="1"
                                    class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                <p v-if="formErrors.hora_fin" class="text-red-500 text-sm mt-1">{{ formErrors.hora_fin
                                }}</p>
                            </div>

                            <!-- Días -->
                            <div class="formulario-grupo col-span-2">
                                <label class="text-gray-700 font-semibold mb-2">Días disponibles</label>
                                <div class="flex flex-wrap gap-4">
                                    <label v-for="dia in diasSemana" :key="dia.valor" class="inline-flex items-center">
                                        <input type="checkbox" v-model="diasSeleccionados" :value="dia.valor"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <span class="ml-2">{{ dia.nombre }}</span>
                                    </label>
                                </div>
                                <p v-if="formErrors.dias" class="text-red-500 text-sm mt-1">{{ formErrors.dias }}</p>
                            </div>

                            <!-- Tipo de Disponibilidad -->
                            <div class="formulario-grupo col-span-2">
                                <label class="text-gray-700 font-semibold">Tipo de disponibilidad</label>
                                <select v-model="disponibilidad.tipo" required
                                    class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                    <option value="unico">Único</option>
                                    <option value="recurrente">Recurrente</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="$router.push('/servicio-agregados')"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all duration-300">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="loading"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transform hover:scale-105 transition-all duration-300 disabled:opacity-50">
                            {{ loading ? 'Guardando...' : 'Guardar Servicio' }}
                        </button>
                    </div>
                </form>

                <!-- Mensaje de éxito/error -->
                <div v-if="message"
                    :class="['mt-4 p-4 rounded-lg', message.type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
                    {{ message.text }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
    name: 'NuevoServicio',
    setup() {
        const router = useRouter();
        const fileInput = ref(null);
        const imagePreview = ref(null);
        const errorMessage = ref('');
        const loading = ref(false);
        const message = ref(null);
        const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
        const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif'];

        const servicio = reactive({
            nombre: '',
            descripcion: '',
            categoria: '',
            costo: '',
            id_ciudad: '',
            imagen: null
        });

        const disponibilidad = reactive({
            fecha: '',
            hora_inicio: '',
            hora_fin: '',
            intervalo: '00:30',
            tipo: 'recurrente'
        });

        const diasSemana = [
            { nombre: 'Lunes', valor: 'lunes' },
            { nombre: 'Martes', valor: 'martes' },
            { nombre: 'Miércoles', valor: 'miercoles' },
            { nombre: 'Jueves', valor: 'jueves' },
            { nombre: 'Viernes', valor: 'viernes' },
            { nombre: 'Sábado', valor: 'sabado' },
            { nombre: 'Domingo', valor: 'domingo' }
        ];

        const diasSeleccionados = ref([]);

        const ciudades = ref([]);

        const formErrors = reactive({
            nombre: '',
            descripcion: '',
            categoria: '',
            costo: '',
            imagen: '',
            id_ciudad: '',
            fecha: '',
            hora_inicio: '',
            hora_fin: '',
            dias: ''
        });

        const categorias = [
            { id: 'consultoria', nombre: 'Consultoría' },
            { id: 'mantenimiento', nombre: 'Mantenimiento' },
            { id: 'diseno', nombre: 'Diseño' },
            { id: 'otro', nombre: 'Otro' }
        ];

        const previewUrl = ref(null);

        const cargarCiudades = async () => {
            try {
                const response = await axios.get('/api/ciudades');
                ciudades.value = response.data;
            } catch (error) {
                console.error('Error al cargar ciudades:', error);
                message.value = {
                    type: 'error',
                    text: 'Error al cargar las ciudades'
                };
            }
        };

        onMounted(() => {
            cargarCiudades();
        });

        const validateForm = () => {
            let isValid = true;
            // Resetear errores
            Object.keys(formErrors).forEach(key => formErrors[key] = '');

            console.log('Validando formulario...', {
                servicio,
                disponibilidad,
                diasSeleccionados: diasSeleccionados.value
            });

            // Validaciones del servicio
            if (!servicio.nombre || servicio.nombre.trim() === '') {
                formErrors.nombre = 'El nombre es requerido';
                isValid = false;
            }

            if (!servicio.descripcion || servicio.descripcion.trim() === '') {
                formErrors.descripcion = 'La descripción es requerida';
                isValid = false;
            }

            if (!servicio.categoria || servicio.categoria.trim() === '') {
                formErrors.categoria = 'La categoría es requerida';
                isValid = false;
            }

            if (!servicio.costo || isNaN(servicio.costo) || parseFloat(servicio.costo) <= 0) {
                formErrors.costo = 'El costo debe ser un número mayor a 0';
                isValid = false;
            }

            if (!servicio.id_ciudad) {
                formErrors.id_ciudad = 'La ciudad es requerida';
                isValid = false;
            }

            // Validaciones de disponibilidad
            if (!disponibilidad.fecha) {
                formErrors.fecha = 'La fecha es requerida';
                isValid = false;
            }

            if (!disponibilidad.hora_inicio) {
                formErrors.hora_inicio = 'La hora de inicio es requerida';
                isValid = false;
            }

            if (!disponibilidad.hora_fin) {
                formErrors.hora_fin = 'La hora de fin es requerida';
                isValid = false;
            }

            if (disponibilidad.hora_inicio && disponibilidad.hora_fin &&
                disponibilidad.hora_inicio >= disponibilidad.hora_fin) {
                formErrors.hora_inicio = 'La hora de inicio debe ser menor a la hora de fin';
                formErrors.hora_fin = 'La hora de fin debe ser mayor a la hora de inicio';
                isValid = false;
            }

            if (!diasSeleccionados.value || diasSeleccionados.value.length === 0) {
                formErrors.dias = 'Debe seleccionar al menos un día';
                isValid = false;
            }

            console.log('Resultado de la validación:', {
                isValid,
                errores: formErrors
            });

            return isValid;
        };

        const formatearHora = (hora) => {
            if (!hora) return '';
            if (hora.split(':').length === 3) return hora;
            return hora + ':00';
        };

        const handleFileSelect = (event) => {
            const file = event.target.files[0];
            if (!file) return;

            // Validar tipo de archivo
            if (!ALLOWED_TYPES.includes(file.type)) {
                errorMessage.value = 'Solo se permiten archivos de imagen (JPEG, PNG, GIF)';
                return;
            }

            // Validar tamaño
            if (file.size > MAX_FILE_SIZE) {
                errorMessage.value = 'La imagen no debe superar los 2MB';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.value = e.target.result;
                errorMessage.value = '';
            };
            reader.onerror = () => {
                errorMessage.value = 'Error al leer el archivo';
            };
            reader.readAsDataURL(file);
        };

        const removeImage = () => {
            imagePreview.value = null;
            errorMessage.value = '';
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        };

        const handleImageUpload = (event) => {
            const file = event.target.files[0];
            if (file) {
                servicio.imagen = file;
                previewUrl.value = URL.createObjectURL(file);
            }
        };

        const guardarServicio = async () => {
            if (!validateForm()) {
                message.value = {
                    type: 'error',
                    text: 'Por favor, corrija los errores en el formulario'
                };
                return;
            }

            loading.value = true;
            message.value = null;

            try {
                const formData = new FormData();
                formData.append('nombre', servicio.nombre);
                formData.append('descripcion', servicio.descripcion);
                formData.append('costo', servicio.costo);
                formData.append('categoria', servicio.categoria);
                formData.append('id_ciudad', servicio.id_ciudad);
                if (servicio.imagen) {
                    formData.append('imagen', servicio.imagen);
                }

                const response = await axios.post('/api/servicios', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                const idServicio = response.data.id_servicio;

                // Luego crear las disponibilidades
                const promesasDisponibilidad = diasSeleccionados.value.map(async (dia) => {
                    const datosDisponibilidad = {
                        id_servicio: idServicio,
                        fecha: disponibilidad.fecha,
                        hora_inicio: formatearHora(disponibilidad.hora_inicio),
                        hora_fin: formatearHora(disponibilidad.hora_fin),
                        intervalo: formatearHora(disponibilidad.intervalo),
                        dias: dia,
                        tipo: disponibilidad.tipo
                    };

                    return axios.post('/api/disponibilidad', datosDisponibilidad);
                });

                await Promise.all(promesasDisponibilidad);

                message.value = {
                    type: 'success',
                    text: '¡Servicio y disponibilidad creados exitosamente!'
                };

                setTimeout(() => {
                    router.push('/servicio-agregados');
                }, 2000);

            } catch (error) {
                console.error('Error al crear servicio:', error);
                message.value = {
                    type: 'error',
                    text: error.response?.data?.message || 'Error al crear el servicio'
                };
            } finally {
                loading.value = false;
            }
        };

        return {
            servicio,
            disponibilidad,
            diasSemana,
            diasSeleccionados,
            fileInput,
            imagePreview,
            errorMessage,
            loading,
            message,
            formErrors,
            categorias,
            ciudades,
            previewUrl,
            handleFileSelect,
            removeImage,
            handleImageUpload,
            guardarServicio
        };
    }
};
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

.subida-archivo {
    border: 2px dashed #e5e7eb;
    padding: 1.5rem;
    text-align: center;
    border-radius: 0.75rem;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.3s ease;
}

.subida-archivo:hover {
    border-color: #3b82f6;
    background-color: #f8fafc;
}

.upload-area {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 150px;
    padding: 1rem;
}

.preview-container {
    position: relative;
    width: 100%;
    max-height: 200px;
    overflow: hidden;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.preview-image {
    width: 100%;
    height: auto;
    max-height: 200px;
    object-fit: contain;
}

.remove-btn {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: #ef4444;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.remove-btn:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.titulo {
    text-align: center;
    padding-bottom: 1rem;
    margin-bottom: 2rem;
    position: relative;
    color: #1f2937;
}

button {
    padding: 0.75rem 2rem;
    border-radius: 0.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

button[type="submit"] {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
}

button[type="submit"]:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 8px rgba(37, 99, 235, 0.3);
}

button[type="button"] {
    border: 2px solid #e5e7eb;
    color: #4b5563;
}

button[type="button"]:hover {
    background-color: #f3f4f6;
    border-color: #d1d5db;
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

@keyframes float {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-5px);
    }
}

.subida-archivo {
    animation: float 6s ease-in-out infinite;
}
</style>
