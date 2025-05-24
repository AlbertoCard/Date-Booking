<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-100 to-white p-6">
        <div class="container bg-white rounded-2xl shadow-2xl overflow-hidden max-w-4xl mx-auto">
            <div class="formulario transform-gpu">
                <h1 class="titulo text-3xl font-bold text-gray-900 relative">
                    Crear Servicio de Restaurante
                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-gradient-to-r from-blue-600 to-blue-700">
                    </div>
                </h1>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Información básica -->
                    <div class="formulario-filas">
                        <div class="formulario-grupo">
                            <label class="text-gray-700 font-semibold">Nombre del Restaurante</label>
                            <input 
                                type="text" 
                                v-model="formData.nombre" 
                                required
                                class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                            />
                        </div>

                        <div class="formulario-grupo">
                            <label class="text-gray-700 font-semibold">Ciudad</label>
                            <select 
                                v-model="formData.id_ciudad" 
                                required
                                class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                            >
                                <option value="">Selecciona una ciudad</option>
                                <option v-for="ciudad in ciudades" :key="ciudad.id_ciudad" :value="ciudad.id_ciudad">
                                    {{ ciudad.nombre }}
                                </option>
                            </select>
                        </div>

                        <div class="formulario-grupo">
                            <label class="text-gray-700 font-semibold">Costo por persona</label>
                            <input 
                                type="number" 
                                v-model="formData.costo" 
                                required
                                min="0"
                                step="0.01"
                                class="precio-input transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                placeholder="0.00"
                            />
                        </div>
                    </div>

                    <div class="formulario-grupo">
                        <label class="text-gray-700 font-semibold">Descripción</label>
                        <textarea 
                            v-model="formData.descripcion" 
                            required
                            class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent min-h-[120px]"
                        ></textarea>
                    </div>

                    <!-- Sección de Disponibilidad -->
                    <div class="mt-8 border-t pt-8">
                        <h2 class="titulo text-2xl font-bold text-gray-900 relative mb-6">
                            Disponibilidad
                            <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-gradient-to-r from-blue-600 to-blue-700">
                            </div>
                        </h2>

                        <div v-for="(disponibilidad, index) in formData.disponibilidad" :key="index" 
                            class="border p-6 rounded-lg mb-4 bg-gray-50">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Horario {{ index + 1 }}</h3>
                                <button 
                                    v-if="formData.disponibilidad.length > 1"
                                    type="button"
                                    @click="eliminarDisponibilidad(index)"
                                    class="text-red-600 hover:text-red-800 transition-colors duration-300"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="formulario-grupo">
                                    <label class="text-gray-700 font-semibold">Día</label>
                                    <select 
                                        v-model="disponibilidad.dias" 
                                        required
                                        class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                    >
                                        <option value="lunes">Lunes</option>
                                        <option value="martes">Martes</option>
                                        <option value="miercoles">Miércoles</option>
                                        <option value="jueves">Jueves</option>
                                        <option value="viernes">Viernes</option>
                                        <option value="sabado">Sábado</option>
                                        <option value="domingo">Domingo</option>
                                    </select>
                                </div>

                                <div class="formulario-grupo">
                                    <label class="text-gray-700 font-semibold">Hora de inicio</label>
                                    <input 
                                        type="text" 
                                        v-model="disponibilidad.hora_inicio" 
                                        required
                                        placeholder="HH:mm"
                                        pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                                        class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        @input="validarHora($event, 'inicio', index)"
                                    />
                                    <p class="text-sm text-gray-500 mt-1">Formato: HH:mm (24 horas)</p>
                                </div>

                                <div class="formulario-grupo">
                                    <label class="text-gray-700 font-semibold">Hora de fin</label>
                                    <input 
                                        type="text" 
                                        v-model="disponibilidad.hora_fin" 
                                        required
                                        placeholder="HH:mm"
                                        pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                                        class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        @input="validarHora($event, 'fin', index)"
                                    />
                                    <p class="text-sm text-gray-500 mt-1">Formato: HH:mm (24 horas)</p>
                                </div>

                                <div class="formulario-grupo">
                                    <label class="text-gray-700 font-semibold">Intervalo entre reservas</label>
                                    <input 
                                        type="text" 
                                        v-model="disponibilidad.intervalo" 
                                        required
                                        placeholder="HH:mm"
                                        pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                                        class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                        @input="validarHora($event, 'intervalo', index)"
                                    />
                                    <p class="text-sm text-gray-500 mt-1">Formato: HH:mm (24 horas)</p>
                                </div>

                                <div class="formulario-grupo">
                                    <label class="text-gray-700 font-semibold">Tipo de Disponibilidad</label>
                                    <select 
                                        v-model="disponibilidad.tipo" 
                                        required
                                        class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                    >
                                        <option value="unica">Única</option>
                                        <option value="recurrente">Recurrente</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button 
                            type="button"
                            @click="agregarDisponibilidad"
                            class="w-full p-3 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all duration-300 flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Agregar Horario
                        </button>
                    </div>

                    <!-- Sección de Mesas -->
                    <div class="mt-8 border-t pt-8">
                        <h2 class="titulo text-2xl font-bold text-gray-900 relative mb-6">
                            Configuración de Mesas
                            <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-gradient-to-r from-blue-600 to-blue-700">
                            </div>
                        </h2>

                        <div v-for="(mesa, index) in formData.mesas" :key="index" 
                            class="border p-6 rounded-lg mb-4 bg-gray-50">
                            <h3 class="text-lg font-semibold mb-4">Mesa {{ index + 1 }}</h3>
                            
                            <div class="formulario-grupo">
                                <label class="text-gray-700 font-semibold">Capacidad (número de personas)</label>
                                <input 
                                    type="number" 
                                    v-model="mesa.personas" 
                                    required
                                    min="1"
                                    class="transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                                />
                            </div>
                        </div>

                        <button 
                            type="button"
                            @click="agregarMesa"
                            class="w-full p-3 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all duration-300 flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Agregar Mesa
                        </button>
                    </div>

                    <div class="flex justify-end space-x-4 mt-8">
                        <button 
                            type="button"
                            @click="$emit('cancel')"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all duration-300"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transform hover:scale-105 transition-all duration-300"
                        >
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

const emit = defineEmits(['submit', 'cancel'])
const ciudades = ref([])

const formData = ref({
    nombre: '',
    descripcion: '',
    costo: '',
    categoria: 'restaurante',
    id_ciudad: '',
    disponibilidad: [
        {
            dias: 'lunes',
            hora_inicio: '',
            hora_fin: '',
            intervalo: '',
            tipo: 'recurrente',
            activo: 1
        }
    ],
    mesas: [
        {
            personas: ''
        }
    ]
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

const agregarMesa = () => {
    formData.value.mesas.push({
        personas: ''
    })
}

const agregarDisponibilidad = () => {
    formData.value.disponibilidad.push({
        dias: 'lunes',
        hora_inicio: '',
        hora_fin: '',
        intervalo: '',
        tipo: 'recurrente',
        activo: 1
    })
}

const eliminarDisponibilidad = (index) => {
    if (formData.value.disponibilidad.length > 1) {
        formData.value.disponibilidad.splice(index, 1)
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

const handleSubmit = () => {
    emit('submit', formData.value)
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