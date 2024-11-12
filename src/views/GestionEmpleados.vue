<template>
    <div class="min-h-screen bg-gray-100 p-8">
      <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Gestión de Empleados</h2>
        <button @click="mostrarModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition duration-300 ease-in-out mb-8">
          Agregar Empleado
        </button>
  
        <!-- Tabla de Empleados -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Puesto</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salario</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="item in empleados" :key="item.empleado_id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.nombre }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getPuestoNombre(item.puesto_id) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ item.salario.toLocaleString() }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button @click="editarEmpleado(item)" class="text-blue-600 hover:text-blue-900 mr-3">Editar</button>
                  <button @click="eliminarEmpleado(item.empleado_id)" class="text-red-600 hover:text-red-900">Eliminar</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
  
        <!-- Modal para Crear/Editar Empleado -->
        <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-2xl font-semibold text-gray-800">{{ empleadoEdit ? "Editar Empleado" : "Nuevo Empleado" }}</h3>
            </div>
            <form @submit.prevent="submitEmpleado" class="p-6">
              <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input id="nombre" v-model="empleadoData.nombre" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required />
              </div>
  
              <div class="mb-4">
                <label for="puesto" class="block text-sm font-medium text-gray-700 mb-1">Puesto</label>
                <select id="puesto" v-model="empleadoData.puesto_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                  <option v-for="puesto in puestos" :key="puesto.puesto_id" :value="puesto.puesto_id">
                    {{ puesto.nombre }}
                  </option>
                </select>
              </div>
  
              <div class="mb-4">
                <label for="salario" class="block text-sm font-medium text-gray-700 mb-1">Salario</label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                  <input id="salario" v-model="empleadoData.salario" type="number" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required />
                </div>
              </div>
  
              <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" v-model="empleadoData.email" type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required />
              </div>
  
              <div class="flex justify-end space-x-3">
                <button type="button" @click="mostrarModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                  Cancelar
                </button>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                  {{ empleadoEdit ? "Actualizar" : "Crear" }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            empleados: [],
            puestos: [],
            empleadoData: {
                nombre: '',
                puesto_id: null,
                salario: 0,
                email: ''
            },
            mostrarModal: false,
            empleadoEdit: false,
        };
    },
    methods: {
        async obtenerEmpleados() {
            try {
                const response = await axios.get('http://localhost/GestionConstructora/backend/empleados.php');
                this.empleados = response.data;
            } catch (error) {
                console.error("Error al obtener empleados:", error);
            }
        },
        async obtenerPuestos() {
            try {
                const response = await axios.get('http://localhost/GestionConstructora/backend/puestos.php');
                this.puestos = response.data;
            } catch (error) {
                console.error("Error al obtener puestos:", error);
            }
        },
        async submitEmpleado() {
            if (this.empleadoEdit) {
                await this.actualizarEmpleado();
            } else {
                await this.crearEmpleado();
            }
            this.mostrarModal = false;
        },
        async crearEmpleado() {
            try {
                const response = await axios.post('http://localhost/GestionConstructora/backend/empleados.php', this.empleadoData);
                console.log("Empleado creado:", response.data);
                await this.obtenerEmpleados();
                this.resetFormulario();
            } catch (error) {
                console.error("Error al crear empleado:", error);
            }
        },
        editarEmpleado(empleado) {
            this.empleadoData = { ...empleado };
            this.empleadoEdit = true;
            this.mostrarModal = true;
        },
        async actualizarEmpleado() {
            try {
                const response = await axios.put('http://localhost/GestionConstructora/backend/empleados.php', this.empleadoData);
                console.log("Empleado actualizado:", response.data);
                await this.obtenerEmpleados();
                this.resetFormulario();
            } catch (error) {
                console.error("Error al actualizar empleado:", error);
            }
        },
        async eliminarEmpleado(id) {
            try {
                const response = await axios.delete('http://localhost/GestionConstructora/backend/empleados.php', { data: { empleado_id: id } });
                console.log("Empleado eliminado:", response.data);
                await this.obtenerEmpleados();
            } catch (error) {
                console.error("Error al eliminar empleado:", error);
            }
        },
        resetFormulario() {
            this.empleadoData = {
                nombre: '',
                puesto_id: null,
                salario: 0,
                email: ''
            };
            this.empleadoEdit = false;
        },
        getPuestoNombre(puesto_id) {
            const puesto = this.puestos.find(p => p.puesto_id === puesto_id);
            return puesto ? puesto.nombre : '';
        }
    },
    mounted() {
        this.obtenerEmpleados();
        this.obtenerPuestos();
    },
};
</script>
