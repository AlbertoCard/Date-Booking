<template>
    <!-- /resources/js/Components/NotFound.vue -->
    <div class="not-found-container">
      <div class="not-found-content">
        <h1 class="not-found-title">404</h1>
        <p class="not-found-message">Lo sentimos, la página que buscas no existe.</p>
        <router-link href="/" class="not-found-link">Volver al inicio</router-link>
      </div>
    </div>
  </template>
  
  <script>
  export default {
  
  }
  </script>
  
  <style>
  .not-found-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    background: #f8fafc;
  }

  .not-found-content {
    text-align: center;
    background: #fff;
    padding: 40px 30px;
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
  }

  .not-found-title {
    font-size: 6rem;
    font-weight: bold;
    color: #e53e3e;
    margin-bottom: 16px;
  }

  .not-found-message {
    font-size: 1.25rem;
    color: #4a5568;
    margin-bottom: 24px;
  }

  .not-found-link {
    display: inline-block;
    padding: 12px 28px;
    background: #3182ce;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
  }

  .not-found-link:hover {
    background: #2b6cb0;
  }
  </style>