<template>
  <div class="dashboard-layout">
    <nav class="navbar">
      <ul class="navbar__menu">
        <li class="navbar__item">
          <router-link to="/" class="navbar__link">
            <i data-feather="home"></i><span>Home</span>
          </router-link>
        </li>
        <li class="navbar__item">
          <router-link to="/gestion-usuarios" class="navbar__link">
            <i data-feather="users"></i><span>Gestión Usuarios</span>
          </router-link>
        </li>
        <li class="navbar__item">
          <router-link to="/gestion-empleados" class="navbar__link">
            <i data-feather="user-check"></i><span>Gestión Empleados</span>
          </router-link>
        </li>
        <li class="navbar__item">
          <router-link to="/gestion-maquinaria" class="navbar__link">
            <i data-feather="settings"></i><span>Gestión Maquinaria</span>
          </router-link>
        </li>
        <li class="navbar__item">
          <router-link to="/gestion-proyectos" class="navbar__link">
            <i data-feather="folder"></i><span>Gestión Proyectos</span>
          </router-link>
        </li>
        <li class="navbar__item">
          <router-link to="/gestion-clientes" class="navbar__link">
            <i data-feather="folder"></i><span>Gestión Clientes</span>
          </router-link>
        </li>
      </ul>
    </nav>
    <div class="contenido">
      <router-view /> <!-- Aquí se mostrarán las vistas cargadas -->
    </div>
  </div>
</template>

<script>
import { onMounted } from 'vue';
import feather from 'feather-icons';

export default {
  name: 'AppDashboard',
  setup() {
    onMounted(() => {
      feather.replace();
    });
  },
};
</script>

<style lang="scss" scoped>
$borderRadius: 10px;
$spacer: 1rem;
$primary: #c55151;
$text: #6a778e;
$linkHeight: $spacer * 3.5;
$timing: 250ms;
$transition: $timing ease all;

.dashboard-layout {
  display: flex;
  min-height: 100vh;
}

.navbar {
  width: 90px;
  background: #040c25;
  border-radius: $borderRadius;
  padding: $spacer 0;
  box-shadow: 0 0 40px rgba(0, 0, 0, 0.03);
  height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  bottom: 0;
}

.contenido {
  flex: 1;
  padding: $spacer * 2;
  margin-left: 90px; // Reserva el espacio de la barra lateral
  overflow-y: auto;
  background: #ffffff;
  border-radius: $borderRadius;
  box-shadow: 0px 4px 8px rgb(179, 204, 231);
}

.navbar__menu {
  list-style: none;
  margin: 0;
  padding: 0;
}

.navbar__link {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  height: $linkHeight;
  width: $spacer * 5.5;
  color: $text;
  transition: $transition;
  border-radius: $borderRadius;

  span {
    position: absolute;
    left: 100%;
    transform: translate(-($spacer * 3));
    margin-left: 1rem;
    opacity: 0;
    pointer-events: none;
    color: $primary;
    background: #040c25;
    padding: $spacer * 0.75;
    transition: $transition;
    border-radius: $borderRadius * 1.75;
  }

  &:hover {
    color: #c55151;
    background-color: #10224e;
  }

  &:hover span {
    opacity: 1;
    transform: translate(0);
  }
}

/* Media Query para dispositivos móviles */
@media (max-width: 768px) {
  .dashboard-layout {
    flex-direction: column;
    height: auto;
  }

  .navbar {
    width: 100%;
    height: auto;
    position: relative;
  }

  .navbar__menu {
    display: flex;
    flex-direction: row;
    overflow-x: auto;
  }

  .navbar__link {
    width: auto;
    flex: 1;
    padding: $spacer;
    span {
      display: none;
    }
  }

  .contenido {
    margin-left: 0;
    padding: $spacer;
  }
}
</style>
