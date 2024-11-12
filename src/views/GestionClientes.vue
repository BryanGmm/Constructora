<template>
    <div class="min-h-screen bg-gray-100 p-8">
      <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Gestión de Clientes</h2>
        <button @click="mostrarModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition duration-300 ease-in-out mb-8">
          Agregar Cliente
        </button>
  
        <!-- Tabla de Clientes -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="cliente in clientes" :key="cliente.cliente_id" class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ cliente.nombre }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ cliente.direccion }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ cliente.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button @click="editarCliente(cliente)" class="text-blue-600 hover:text-blue-900 mr-3 transition-colors duration-200">Editar</button>
                  <button @click="eliminarCliente(cliente.cliente_id)" class="text-red-600 hover:text-red-900 transition-colors duration-200">Eliminar</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
  
        <!-- Modal para Crear/Editar Cliente -->
        <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-2xl font-semibold text-gray-800">{{ clienteEdit ? "Editar Cliente" : "Nuevo Cliente" }}</h3>
            </div>
            <form @submit.prevent="submitCliente" class="p-6">
              <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input id="nombre" v-model="clienteData.nombre" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required />
              </div>
  
              <div class="mb-4">
                <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <input id="direccion" v-model="clienteData.direccion" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
              </div>
  
              <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" v-model="clienteData.email" type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
              </div>
  
              <div class="flex justify-end space-x-3">
                <button type="button" @click="mostrarModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                  Cancelar
                </button>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                  {{ clienteEdit ? "Actualizar" : "Crear" }}
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
            clientes: [],
            clienteData: {
                nombre: '',
                direccion: '',
                email: '',
            },
            mostrarModal: false,
            clienteEdit: false,
        };
    },
    methods: {
        obtenerClientes() {
            axios.get('http://localhost/GestionConstructora/backend/clientes.php')
                .then(response => {
                    this.clientes = response.data;
                })
                .catch(error => console.error("Error al obtener clientes:", error));
        },
        submitCliente() {
            if (this.clienteEdit) {
                this.actualizarCliente();
            } else {
                this.crearCliente();
            }
            this.mostrarModal = false;
        },
        crearCliente() {
            axios.post('http://localhost/GestionConstructora/backend/clientes.php', this.clienteData)
                .then(response => {
                    console.log("Cliente creado:", response.data);
                    this.obtenerClientes();
                    this.resetFormulario();
                    this.mostrarModal = false;
                })
                .catch(error => console.error("Error al crear cliente:", error));
        },
        editarCliente(cliente) {
            this.clienteData = { ...cliente };
            this.clienteEdit = true;
            this.mostrarModal = true;
        },
        actualizarCliente() {
            axios.put('http://localhost/GestionConstructora/backend/clientes.php', this.clienteData)
                .then(response => {
                    console.log("Cliente actualizado:", response.data);
                    this.obtenerClientes();
                    this.resetFormulario();
                })
                .catch(error => console.error("Error al actualizar cliente:", error));
        },
        eliminarCliente(id) {
            axios.delete('http://localhost/GestionConstructora/backend/clientes.php', { data: { cliente_id: id } })
                .then(response => {
                    console.log("Cliente eliminado:", response.data);
                    this.obtenerClientes();
                })
                .catch(error => console.error("Error al eliminar cliente:", error));
        },
        resetFormulario() {
            this.clienteData = {
                nombre: '',
                direccion: '',
                email: '',
            };
            this.clienteEdit = false;
        },
    },
    mounted() {
        this.obtenerClientes();
    },
};
</script>
