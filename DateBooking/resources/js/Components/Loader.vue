<template>
    <div v-if="visible" class="loader-overlay">
        <div class="spinner"></div>
    </div>
</template>

<script>
export default {
    props: {
        visible: {
            type: Boolean,
            default: false
        }
    }
}
</script>

<style scoped>
.loader-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255,255,255,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.spinner {
    width: 60px;
    height: 60px;
    border: 6px solid #ccc;
    border-top: 6px solid #385c7e;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@media (max-width: 600px) {
    .spinner {
        width: 40px;
        height: 40px;
        border-width: 4px;
    }
    .loader-overlay {
        background: rgba(255,255,255,0.85);
        align-items: flex-start;
        padding-top: 33vh;
    }
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>