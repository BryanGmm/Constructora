<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Gestión de Maquinaria</h1>

    <!-- Filtros de búsqueda -->
    <div class="flex space-x-4 mb-4">
      <input
        v-model="filters.nombre"
        type="text"
        placeholder="Buscar por nombre"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
      />
      <select
        v-model="filters.estado"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
      >
        <option value="">Todos los estados</option>
        <option value="Disponible">Disponible</option>
        <option value="En uso">En uso</option>
        <option value="En mantenimiento">En mantenimiento</option>
      </select>
    </div>

    <!-- Botón para abrir el modal de Ingresar Maquinaria -->
    <button @click="openModal()" style="background-color: #2c3a4e;" class="text-white font-semibold px-4 py-2 rounded-md mb-4">
      Ingresar Maquinaria
    </button>

    <!-- Modal para crear o editar maquinaria -->
    <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4 py-12 text-center">
        <div class="fixed inset-0 transition-opacity" @click="closeModal()">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="submitForm" class="bg-white p-6">
            <h2 class="text-xl font-semibold mb-4">{{ form.maquinaria_id ? 'Editar Maquinaria' : 'Ingresar Maquinaria' }}</h2>
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">Nombre</label>
              <input v-model="form.nombre" id="nombre" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="descripcion">Descripción</label>
              <textarea v-model="form.descripcion" id="descripcion" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="estado">Estado</label>
              <select v-model="form.estado" id="estado" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="Disponible">Disponible</option>
                <option value="En uso">En uso</option>
                <option value="En mantenimiento">En mantenimiento</option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="fecha_adquisicion">Fecha de Adquisición</label>
              <input v-model="form.fecha_adquisicion" id="fecha_adquisicion" type="date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="costo">Costo</label>
              <input v-model="form.costo" id="costo" type="number" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="tipo">Tipo</label>
              <input v-model="form.tipo" id="tipo" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex justify-end">
              <button type="button" @click="closeModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">Cancelar</button>
              <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ form.maquinaria_id ? 'Actualizar' : 'Crear' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Tabla de maquinaria -->
    <table class="table-auto w-full bg-white shadow-md rounded mb-4">
      <thead>
        <tr>
          <th class="px-4 py-2">ID</th>
          <th class="px-4 py-2">Nombre</th>
          <th class="px-4 py-2">Descripción</th>
          <th class="px-4 py-2">Estado</th>
          <th class="px-4 py-2">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in filteredMaquinarias" :key="item.maquinaria_id">
          <td class="border px-4 py-2">{{ item.maquinaria_id }}</td>
          <td class="border px-4 py-2">{{ item.nombre }}</td>
          <td class="border px-4 py-2">{{ item.descripcion }}</td>
          <td class="border px-4 py-2">{{ item.estado }}</td>
          <td class="border px-4 py-2">
            <button @click="editMaquinaria(item)" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-1 px-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
              Editar
            </button>
            <button @click="deleteMaquinaria(item.maquinaria_id)" class="bg-red-500 hover:bg-red-700 text-white font-semibold py-1 px-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
              Eliminar
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      showModal: false,
      maquinarias: [],
      filters: {
        nombre: '',
        estado: ''
      },
      form: {
        maquinaria_id: null,
        nombre: '',
        descripcion: '',
        estado: 'Disponible',
        fecha_adquisicion: '',
        costo: '',
        tipo: '',
      },
    };
  },
  computed: {
    filteredMaquinarias() {
      return this.maquinarias.filter((maquinaria) => {
        const matchesNombre = maquinaria.nombre.toLowerCase().includes(this.filters.nombre.toLowerCase());
        const matchesEstado = this.filters.estado === '' || maquinaria.estado === this.filters.estado;
        return matchesNombre && matchesEstado;
      });
    },
  },
  methods: {
    async fetchMaquinarias() {
      try {
        const response = await axios.get('http://localhost/GestionConstructora/backend/maquinaria.php');
        this.maquinarias = response.data;
      } catch (error) {
        console.error("Error al obtener maquinarias:", error);
      }
    },
    async submitForm() {
      try {
        if (this.form.maquinaria_id) {
          // Update
          await axios.put('http://localhost/GestionConstructora/backend/maquinaria.php', this.form);
        } else {
          // Create
          await axios.post('http://localhost/GestionConstructora/backend/maquinaria.php', this.form);
        }
        await this.fetchMaquinarias();
        this.closeModal();
      } catch (error) {
        console.error("Error al enviar formulario:", error);
      }
    },
    editMaquinaria(item) {
      this.form = { ...item };  // Recupera los datos de la maquinaria a editar
      this.openModal();
    },
    async deleteMaquinaria(id) {
      try {
        await axios.delete('http://localhost/GestionConstructora/backend/maquinaria.php', {
          data: { maquinaria_id: id },
        });
        await this.fetchMaquinarias();
      } catch (error) {
        console.error("Error al eliminar maquinaria:", error);
      }
    },
    openModal() {
      this.showModal = true;
      this.resetForm();
    },
    closeModal() {
      this.showModal = false;
    },
    resetForm() {
      this.form = {
        maquinaria_id: null,
        nombre: '',
        descripcion: '',
        estado: 'Disponible',
        fecha_adquisicion: '',
        costo: '',
        tipo: '',
      };
    },
  },
  created() {
    this.fetchMaquinarias();
  },
};
</script>

<style scoped>
/* Puedes agregar estilos personalizados aquí */
</style>
