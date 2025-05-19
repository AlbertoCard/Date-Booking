<template>
    <div class="mis-reservas">

    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            // Variables reactivas aquí
            reservas: [],
        };
    },
    methods: {
        // Métodos aquí
    },
    mounted() {
        const userId = this.$route.params.id;
        axios.get(`/api/reservas/${userId}`)
            .then(response => {
                this.reservas = response.data.reservas;
                console.log('Reservas obtenidas:', this.reservas);
            })
            .catch(error => {
                console.error('Error al obtener las reservas:', error);
            });
    }
};
</script>

<style scoped>
</style>