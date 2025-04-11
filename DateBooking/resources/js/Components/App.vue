<template>
    <div>
      <header>
        <nav>
          <ul>
            <li>
              <router-link to="/inicio">Inicio</router-link>
            </li>
            <li>
              <router-link to="/nosotros">Nosotros</router-link>
            </li>
          </ul>
        </nav>
      </header>
      <main>
        <router-view></router-view>
      </main>
    </div>
  </template>
  
  <script>
  export default {
    data: () => ({}),
  };
  </script>