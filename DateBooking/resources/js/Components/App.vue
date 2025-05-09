<template>
    <div>
      <header class="hero">
        <nav class="nav container">
          <div class="nav_logo">
              <img src="" alt="Logo" class="logo ">
              <router-link to="/inicio">DATE-BOOKING</router-link>
          </div>
          <ul class="nav_link nav_link--menu">
            <li class="nav_items">
              <input type="search" v-model="searchText" placeholder="Buscar...">
            </li>
            <li class="nav_items">
              <button type="button" @click="redirectToSearch">Buscar</button>
            </li>
            <li class="nav_items">
              <router-link to="/nosotros">Usuario</router-link>
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
    data: () => ({
      searchText: '',
    }),
    methods: {
      redirectToSearch() {
        if (this.searchText) {
          this.$router.push({ path: '/busqueda', query: { q: this.searchText } });
        }
        else {
          alert('Por favor, ingrese un término de búsqueda.');
        }
      },
    },
  };
  </script>

<style scoped>
.logo {
  width: 50px;
  height: auto;
}

nav ul {
  list-style: none;
  display: flex;
  align-items: center;
  gap: 10px;
}

nav ul li {
  margin: 0;
}

input[type="search"] {
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

button {
  padding: 5px 10px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #0056b3;
}


.container{
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
    overflow: hidden;
    padding: var(--padding-container);
}

.hero{
    width: 100%;
    position: relative;
    display: grid;
    grid-template-rows: 100px 1fr;
}

.nav{
    --padding-container: 0;
    height: 100%;
    display: flex;
    align-items: center;
}

.nav_link{
    margin-left: auto;
    padding: 0;
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: max-content;
    gap: 2em;
}

.nav_links{
    color: #fff;
    text-decoration: none;
}

.nav_items{
    list-style: none;
}

.nav_logo{
    display: flex;
    align-items: center;
    gap: 1em;
}

</style>