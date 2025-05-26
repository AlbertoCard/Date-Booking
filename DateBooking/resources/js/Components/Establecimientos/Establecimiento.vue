<template>
    <div class="establecimiento-info">
        <Loader :visible="cargando" />
        <div class="header">
            <div class="header-flex">
                <button @click="$router.back()" class="volver">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    <span style="font-size: 1rem; color: #334155;">Volver</span>
                </button>
                <div>
                    <h1 class="nombre">{{ establecimiento?.nombre }}</h1>
                    <span class="estado" :class="establecimiento?.estado === 'activo' ? 'activo' : 'inactivo'">
                        {{ establecimiento?.estado }}
                    </span>
                </div>
            </div>
            <div class="acciones">
                <button v-if="establecimiento?.estado"
                    :class="['estado-btn', establecimiento?.estado === 'activo' ? 'activo' : 'inactivo']"
                    @click="toggleEstado">
                    {{ establecimiento?.estado === 'activo' ? 'Suspender' : 'Autorizar' }}
                </button>
                <button v-if="establecimiento?.estado" class="estado-btn rechazar" @click="rechazar">
                    Rechazar
                </button>
            </div>
        </div>
        <div class="info-list">
            <div class="info-item">
                <span class="label">Dirección:</span>
                <span>{{ establecimiento?.direccion }}</span>
            </div>
            <div class="info-item">
                <span class="label">Teléfono:</span>
                <span>{{ establecimiento?.telefono }}</span>
            </div>
            <div class="info-item">
                <span class="label">RFC:</span>
                <span>{{ establecimiento?.rfc }}</span>
            </div>
            <div class="info-item">
                <span class="label">Stripe Account ID:</span>
                <span>{{ establecimiento?.stripe_account_id || 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Creado en:</span>
                <span>{{ establecimiento?.created_at }}</span>
            </div>
            <div class="info-item">
                <span class="label">Actualizado en:</span>
                <span>{{ establecimiento?.updated_at }}</span>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Loader from '../Loader.vue';

export default {
    components: {
        Loader
    },
    data() {
        return {
            establecimiento: [],
            cargando: true,
        }
    },
    mounted() {
        this.cargando = true;
        const name = this.$route.params.nombre;
        axios.get(`/api/establecimientos/${name}`)
            .then(response => {
                this.establecimiento = response.data;
            })
            .catch(error => {
                console.error("Error fetching establecimiento data:", error);
            })
            .finally(() => {
                this.cargando = false;
            });
    },
    methods: {
        toggleEstado() {
            this.cargando = true;
            const id = this.establecimiento.id_establecimiento;
            axios.put(`/api/establecimientos/estado/${id}`)
                .then(response => {
                    this.establecimiento = response.data;
                })
                .catch(error => {
                    console.error("Error toggling estado:", error);
                })
                .finally(() => {
                    this.cargando = false;
                });
        },
        rechazar() {
            this.cargando = true;
            const id = this.establecimiento.id_establecimiento;
            axios.put(`/api/establecimientos/rechazar/${id}`)
                .then(response => {
                    this.establecimiento = response.data;
                })
                .catch(error => {
                    console.error("Error rejecting establecimiento:", error);
                })
                .finally(() => {
                    this.cargando = false;
                });
        }
    }
}
</script>

<style scoped>
.establecimiento-info {
    max-width: 1000px;
    max-height: 80vh;
    margin: 1rem auto;
    margin-top: 0px;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1rem;
    justify-content: space-between;
}


.volver {
    margin-left: -1rem;
    margin-top: -1rem;
    display: flex;
    align-items: center;
    background: #fff;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    color: #334155;
}

.header {
    position: relative;
}

.nombre {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    color: #1e293b;
    display: inline-block;
    vertical-align: middle;
    margin-right: 0.75rem;
}
.estado {
    display: inline-block;
    margin-top: 0.25rem;
    vertical-align: middle;
    padding: 0.2em 0.8em;
    border-radius: 999px;
    font-size: 0.95rem;
    font-weight: 600;
    letter-spacing: 0.02em;
}

.estado.activo {
    background: #d1fae5;
    color: #059669;
}

.estado.inactivo {
    background: #fee2e2;
    color: #b91c1c;
}

.acciones {
    display: flex;
    gap: 0.5rem;
    margin-left: auto;
}

.estado-btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
}

.estado-btn.inactivo {
    background: #4f46e5;
    color: #fff;
}

.estado-btn.activo {
    background: #dc2626;
    color: #fff;
}



.info-list {
    margin-top: 1rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem 2rem;
    background: #f3f4f6;
    padding: 1.5rem 2rem;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    overflow-x: auto;
}



.info-item {
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 6px;
    padding: 1rem 1.2rem;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    transition: box-shadow 0.2s;
}

.info-item:hover {
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.08);
}

.label {
    font-weight: 700;
    color: #6366f1;
    font-size: 0.97rem;
    margin-bottom: 0.2rem;
}

.info-item span:not(.label) {
    color: #334155;
    font-size: 1.08rem;
    word-break: break-all;
}

/*  */


.establecimiento-img {
    max-width: 100%;
    margin-top: 1rem;
    border-radius: 4px;
}

.estado-btn.rechazar {
    background: gray;
    color: #fff;
    border: none;
    transition: all .5s ease;
}

.estado-btn.rechazar:hover {
    background: #991b1b;
}

@media (max-width: 700px) {
    .info-list {
        grid-template-columns: 1fr;
        padding: 1rem 0.5rem;
    }
}
</style>