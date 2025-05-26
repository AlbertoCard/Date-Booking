<template>
  <header class="hero">
    <div class="div_btnMenu">
      <button @click="toggleSidebar" class="btnMenu">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
      <router-link to="/">
        <img src="../../../public/images/Date-booking_logo_provicional.png" alt="logo" class="logo_img">
      </router-link>
      <router-link to="/" class="nombre">DATE-BOOKING</router-link>
    </div>

    <div class="div_search">
      <input type="text" v-model="searchText" class="search_input" placeholder="Comienza tu busqueda..."
        @keyup.enter="redirectToSearch" />
      <button @click="redirectToSearch" class="btnSearch">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 25 25" stroke-width="1.5" stroke="currentColor"
          class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
      </button>
    </div>

    <div class="div_user" ref="userDropdown">
      <button class="btnUser" @click="toggleUserDropdown">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="size-6">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        <span class="userName">{{ userName }}</span>
      </button>
      <div v-if="showUserDropdown" class="user-dropdown">
        <template v-if="!isAuthenticated">
          <button class="dropdown-item" @click="login">Iniciar Sesión</button>
          <router-link to="/registro" class="dropdown-item">Registrarse</router-link>
        </template>
        <template v-else>
          <button class="dropdown-item" @click="logout">Cerrar Sesión</button>
        </template>
      </div>
    </div>
  </header>
</template>

<script>
import axios from 'axios';
import { signOut } from 'firebase/auth';
import { auth } from '../firebase';

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
      showUserDropdown: false,
      isAuthenticated: false,
      userData: null
    };
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside);
    // Verificar si hay datos de usuario en localStorage
    const userDataStr = localStorage.getItem('userData');
    if (userDataStr) {
      this.userData = JSON.parse(userDataStr);
      this.isAuthenticated = true;
    }
  },
  computed: {
    userName() {
      return this.userData ? this.userData.nombre : 'Invitado';
    }
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
    toggleUserDropdown() {
      this.showUserDropdown = !this.showUserDropdown;
    },
    closeDropdown() {
      this.showUserDropdown = false;
    },
    handleClickOutside(event) {
      const dropdown = this.$refs.userDropdown;
      if (dropdown && !dropdown.contains(event.target)) {
        this.closeDropdown();
      }
    },
    login() {
      this.$router.push('/login');
      this.closeDropdown();
    },
    async logout() {
      try {
        // Primero actualizamos el estado en la base de datos
        if (auth.currentUser) {
          await axios.put(`/api/usuarios/${auth.currentUser.uid}/activo`);
        }

        // Luego cerramos sesión en Firebase
        await signOut(auth);

        // Limpiar datos del usuario
        localStorage.removeItem('userData');
        this.userData = null;
        this.isAuthenticated = false;

        // Redireccionar al inicio
        await this.$router.push('/');

        // Cerrar el dropdown
        this.closeDropdown();
      } catch (error) {
        console.error('Error al cerrar sesión:', error);
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
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.div_btnMenu {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.btnMenu {
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
  min-width: 55px;
  width: 60px;
  height: auto;
  background: none;
  border-radius: 0;
}

.div_search {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 70%;
  min-width: 100px;
  max-width: 600px;
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
  width: 100%;
  transition: all ease-in-out .5s;
  margin-right: -2rem;
  overflow: hidden;
}

.search_input:focus {
  outline: none;
  background-color: #f0eeee;
  border: 1px solid #2563eb;
}

.search_input:focus+.btnSearch {
  background-color: #f0eeee;
}

.btnSearch {
  border: none;
  padding: 8px;
  border-radius: 15px;
  margin-left: -20px;
}

.btnSearch:hover {
  cursor: pointer;
}

.div_user {
  flex-shrink: 0;
}

.btnUser {
  background: none;
  border: none;
  padding: 10px;
  border-radius: 10px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btnUser:hover {
  background-color: #e0e7ff;
}

.user-dropdown {
  position: absolute;
  right: 0;
  top: 60px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  min-width: 140px;
  z-index: 100;
  display: flex;
  flex-direction: column;
  padding: 0.5rem 0;
}

.dropdown-item {
  background: none;
  border: none;
  text-align: left;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  color: #374151;
  cursor: pointer;
  transition: all 0.3s ease;
}

.dropdown-item:hover {
  background: #f3f4f6;
  color: #2563eb;
}

.nombre {
  color: inherit;
  font-weight: normal;
  font-size: inherit;
  text-shadow: none;
}

@media (max-width: 700px) {
  .hero {
    justify-content: start;
  }

  .div_user {
    width: 10%;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .nombre {
    display: none;
  }

  .btnSearch {
    display: none;
  }

  .div_btnMenu {
    gap: 0px;
  }


  .userName {
    display: none;
  }
}
</style>