<template>
    <header class="hero">
      <div class="div_btnMenu">
        <button @click="toggleSidebar" class="btnMenu">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <router-link to="/" >
          <img src="../../../public/images/Date-booking_logo_provicional.png" alt="logo" class="logo_img">
        </router-link>
        <router-link to="/">DATE-BOOKING</router-link>
      </div>
  
      <div class="div_search">
        <input type="text" v-model="searchText" class="search_input" placeholder="Comienza tu busqueda...">
        <button @click="redirectToSearch" class="btnSearch">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 25 25" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
        </button>
      </div>
      
      <div class="div_user">
        <button @click="redirectToUser" class="btnUser">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
          </svg>
        </button>
      </div>
    </header>
  </template>
  
  <script>
  export default {
    props: {
      toggleSidebar: {
        type: Function,
        required: true,
      },
    },
    data() {
      return {
        searchText: '',
      };
    },
    methods: {
      redirectToSearch() {
        if (this.searchText) {
          this.$router.push({ path: '/busqueda/' + this.searchText }).then(() => {
            this.$router.go(0); 
          });
        }
        else {
          alert('Por favor, ingrese un término de búsqueda.');
        }
      },
    },
  };
  </script>

  
  <style scoped>
  .hero {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1rem;
    height: 80px;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .div_btnMenu{
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .btnMenu{
    background: none;
    border: none;
    padding: 8px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  
  .btnMenu:hover {
    background-color: #e0e7ff;
  }
  
  .icon {
    width: 24px;
    height: 24px;
    color: #374151;
  }
  
  .div_logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-left: 3rem;
  }
  
  .logo_img {
    width: 60px;
    height: auto;
  }
  
  .div_search{
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .search {
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-align: center;
  }
  
  .search_input {
    font-family: inherit;
    font-size: inherit;
    background-color: #f4f2f2;
    border: none;
    color: #646464;
    padding: 0.7rem 1rem;
    border-radius: 30px;
    width: 40rem;
    transition: all ease-in-out .5s;
    margin-right: -2rem;
  }
  
  .search_input:hover, .search__input:focus {
    box-shadow: 0 0 1em #00000013;
  }
  
  .search_input:focus {
    outline: none;
    background-color: #f0eeee;
  }
  
  .search_input::-webkit-input-placeholder {
    font-weight: 100;
    color: #ccc;
  }
  
  .search_input:focus + .btnSearch {
    background-color: #f0eeee;
  }
  
  .btnSearch {
    border: none;
    padding: 8px;
    border-radius: 6px;
    margin-left: -20px;
  }
  
  .btnSearch:hover {
    cursor: pointer;
  }
  
  .div_user {
    flex-shrink: 0;
  }
  
  .btnUser{
    background: none;
    border: none;
    padding: 10px;
    border-radius: 10px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  
  .btnUser:hover {
    background-color: #e0e7ff;
  }
  </style>