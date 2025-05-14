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
                            <select v-model="servicio.categoria" required
                                class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                <option disabled value="">Selecciona la categoría</option>
                                <option>Consultoría</option>
                                <option>Mantenimiento</option>
                                <option>Diseño</option>
                                <option>Otro</option>
                            </select>
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
                        <label class="text-gray-700 font-semibold">Imagen del servicio</label>
                        <div class="subida-archivo">
                            <input type="file" ref="fileInput" @change="handleFileSelect" accept="image/*"
                                class="hidden" />

                            <!-- Vista previa de la imagen -->
                            <div v-if="imagePreview" class="preview-container">
                                <img :src="imagePreview" alt="Vista previa" class="preview-image" />
                                <button @click="removeImage" class="remove-btn">
                                    Eliminar
                                </button>
                            </div>

                            <!-- Área de selección cuando no hay imagen -->
                            <div v-else @click="$refs.fileInput.click()" class="upload-area">
                                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span class="text-gray-600">Seleccionar imagen</span>
                            </div>
                        </div>
                        <p v-if="errorMessage" class="text-red-500 text-sm mt-1">{{ errorMessage }}</p>
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
import { ref, reactive } from 'vue';
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

        const servicio = reactive({
            nombre: '',
            descripcion: '',
            categoria: '',
            costo: ''
        });

        const handleFileSelect = (event) => {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    errorMessage.value = 'La imagen no debe superar los 2MB';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePreview.value = e.target.result;
                    errorMessage.value = '';
                };
                reader.readAsDataURL(file);
            }
        };

        const removeImage = () => {
            imagePreview.value = null;
            errorMessage.value = '';
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        };

        const guardarServicio = async () => {
            loading.value = true;
            message.value = null;

            try {
                const response = await axios.post('http://localhost:8000/api/servicios', servicio);

                message.value = {
                    type: 'success',
                    text: '¡Servicio creado exitosamente!'
                };

                // Esperar 2 segundos y redirigir
                setTimeout(() => {
                    router.push('/servicio-agregados');
                }, 2000);

            } catch (error) {
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
            fileInput,
            imagePreview,
            errorMessage,
            loading,
            message,
            handleFileSelect,
            removeImage,
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
}

.formulario-grupo input,
.formulario-grupo select,
.formulario-grupo textarea {
    padding: 0.75rem;
    border-radius: 0.75rem;
    border: 1px solid rgba(37, 99, 235, 0.1);
    font-size: 0.875rem;
    width: 100%;
    background: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.formulario-grupo input:focus,
.formulario-grupo select:focus,
.formulario-grupo textarea:focus {
    outline: none;
    border-color: transparent;
}

textarea {
    resize: vertical;
}

.fecha-inputs {
    display: flex;
    gap: 0.75rem;
}

.fecha-inputs input {
    width: 100%;
}

.precio-input {
    color: #2563eb;
    font-weight: 600;
}

.subida-archivo {
    border: 2px dashed #e5e7eb;
    padding: 1.5rem;
    text-align: center;
    border-radius: 0.75rem;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.subida-archivo:hover {
    border-color: #2563eb;
}

.upload-area {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 150px;
}

.preview-container {
    position: relative;
    width: 100%;
    max-height: 200px;
    overflow: hidden;
    border-radius: 0.5rem;
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
}

.remove-btn:hover {
    background: #dc2626;
}

.titulo {
    text-align: center;
    padding-bottom: 1rem;
    margin-bottom: 2rem;
    position: relative;
}

button {
    padding: 0.75rem 2rem;
    border-radius: 0.75rem;
    font-weight: 600;
    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
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

    .fecha-inputs {
        flex-direction: row;
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
