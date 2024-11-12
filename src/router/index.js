import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../components/Dashboard.vue'
import HomeView from '../views/HomeView.vue'
import GestionUsuarios from '../views/GestionUsuarios.vue'
import GestionEmpleados from '../views/GestionEmpleados.vue'
import GestionMaquinaria from '../views/GestionMaquinaria.vue'
import GestionProyectos from '../views/GestionProyectos.vue'
import GestionClientes from '@/views/GestionClientes.vue'

const routes = [
  {
    path: '/',
    component: Dashboard, // El Dashboard será el contenedor
    children: [
      {
        path: '',
        name: 'home',
        component: HomeView,
      },
      {
        path: 'gestion-usuarios',
        name: 'gestionUsuarios',
        component: GestionUsuarios,
      },
      {
        path: 'gestion-empleados',
        name: 'gestionEmpleados',
        component: GestionEmpleados,
      },
      {
        path: 'gestion-maquinaria',
        name: 'gestionMaquinaria',
        component: GestionMaquinaria,
      },
      {
        path: 'gestion-proyectos',
        name: 'gestionProyectos',
        component: GestionProyectos,
      },
      {
        path: 'gestion-clientes',
        name: 'gestionClientes',
        component: GestionClientes,
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
})

export default router
