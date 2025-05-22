<template>
    <div v-if="modelValue" class="sidebar" ref="sidebarRef">
        <ul class="sidebar-list">
            <li class="sidebar-item close" style="justify-content: flex-end;">
                <button @click="closeSidebar" class="sidebar-link" style="background: none; border: none; color: #374151; padding: 0.5rem; font-size: 1.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="sidebar-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </li>

            <!-- Opción de Inicio para todos -->
            <li class="sidebar-item">
                <router-link
                    to="/"
                    @click="handleOptionClick"
                    class="sidebar-link"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="sidebar-icon">
                       <path
                            stroke-linecap="round"
                            stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Inicio
                </router-link>
            </li>

            <!-- Opciones para usuarios autenticados -->
            <template v-if="isAuthenticated">
                <!-- Opciones para clientes -->
                <template v-if="userRole === 'cliente'">
                    <li class="sidebar-item">
                        <router-link
                            to="/reservas"
                            @click="handleOptionClick"
                            class="sidebar-link"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="sidebar-icon">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                            </svg>
                            Mis Reservas
                        </router-link>
                    </li>
                </template>

                <!-- Opciones para establecimientos -->
                <template v-if="userRole === 'establecimiento'">
                    <li class="sidebar-item">
                        <router-link
                            to="/nuevo-servicio"
                            @click="handleOptionClick"
                            class="sidebar-link"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="sidebar-icon">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Nuevo Servicio
                        </router-link>
                    </li>
                    <li class="sidebar-item">
                        <router-link
                            to="/servicio-agregados"
                            @click="handleOptionClick"
                            class="sidebar-link"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="sidebar-icon">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                            Mis Servicios
                        </router-link>
                    </li>
                </template>

                <!-- Opciones para administradores -->
                <template v-if="userRole === 'admin'">
                    <li class="sidebar-item">
                        <router-link
                            to="/servicios"
                            @click="handleOptionClick"
                            class="sidebar-link"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="sidebar-icon">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                            Gestión de Servicios
                        </router-link>
                    </li>
                    <li class="sidebar-item">
                        <router-link
                            to="/validaciones"
                            @click="handleOptionClick"
                            class="sidebar-link"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="sidebar-icon">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Validaciones
                        </router-link>
                    </li>
                </template>

                <!-- Opciones comunes para usuarios autenticados -->
                <li class="sidebar-item">
                    <router-link
                        to="/ajustes"
                        @click="handleOptionClick"
                        class="sidebar-link"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="sidebar-icon">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        Ajustes
                    </router-link>
                </li>
            </template>
        </ul>
    </div>
</template>

<script setup>
import { defineProps, defineEmits, ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { auth } from '../firebase'

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    }
})

const emit = defineEmits(['update:modelValue'])
const sidebarRef = ref(null)
const userRole = ref(null)
const isAuthenticated = ref(false)
let ignoreClick = false

// Función para limpiar el estado del usuario
const limpiarEstadoUsuario = () => {
    userRole.value = null
    isAuthenticated.value = false
    localStorage.removeItem('userData')
}

// Función para obtener el rol del usuario
const obtenerRolUsuario = async () => {
    try {
        const userData = localStorage.getItem('userData')
        if (userData) {
            const parsedData = JSON.parse(userData)
            userRole.value = parsedData.rol
            isAuthenticated.value = true
        } else {
            limpiarEstadoUsuario()
        }
    } catch (error) {
        console.error('Error al obtener el rol del usuario:', error)
        limpiarEstadoUsuario()
    }
}

// Escuchar cambios en la autenticación
const unsubscribe = auth.onAuthStateChanged((user) => {
    if (user) {
        obtenerRolUsuario()
    } else {
        limpiarEstadoUsuario()
    }
})

function isMobile() {
    return window.innerWidth < 768
}

function handleClickOutside(event) {
    if (!isMobile()) return // Solo cerrar en móvil
    if (ignoreClick) {
        ignoreClick = false
        return
    }
    if (props.modelValue && sidebarRef.value && !sidebarRef.value.contains(event.target)) {
        emit('update:modelValue', false)
    }
}

function closeSidebar() {
    ignoreClick = true
    emit('update:modelValue', false)
}

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside)
    obtenerRolUsuario()
})

onBeforeUnmount(() => {
    document.removeEventListener('mousedown', handleClickOutside)
    unsubscribe() // Limpiar el listener de autenticación
})

function handleOptionClick() {
    if (isMobile()) {
        emit('update:modelValue', false)
    }
}
</script>

<style>
.sidebar {
    height: 100vh;
    width: 18rem;
    background: #fff;
    padding: 1.25rem;
    box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px 0 rgba(0,0,0,0.06);
    border-radius: 0.375rem;
    display: flex;
    flex-direction: column;
}
.sidebar-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    flex-grow: 1;
    padding: 0;
    margin: 0;
    list-style: none;
}
.sidebar-item {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: 600;
    width: 100%;
    white-space: nowrap;
}
.sidebar-link {
    display: flex;
    align-items: center;
    width: 100%;
    gap: 1rem;
    padding: 1rem;
    font-weight: 600;
    border-radius: 9999px;
    background-size: cover;
    color: #374151;
    text-decoration: none;
    transition: all 0.2s linear;
}
.sidebar-link:hover {
    background: #dbeafe;
    box-shadow: inset 0 2px 8px 0 rgba(59,130,246,0.1);
}
.sidebar-link:focus {
    background: linear-gradient(to right, #60a5fa, #2563eb);
    color: #fff;
}
.sidebar-icon {
    width: 1.5rem;
    height: 1.5rem;
}

.close {
    display: none;
}

@media(max-width: 768px) {
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 50;
        overflow-y: auto;
        overflow-x: hidden;
        background-color: #fff;
        padding: 1rem;
    }
    .close {
        display: flex;
    }
}
</style>