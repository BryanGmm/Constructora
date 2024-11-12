<template>
    <div class="min-h-screen bg-gray-100 p-8">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Gestión de Maquinaria</h2>
            <button @click="abrirModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-md transition duration-300 ease-in-out transform hover:scale-105 mb-6">
                Agregar Maquinaria
            </button>

            <!-- Tabla de Maquinaria -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nombre</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Descripción</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha de Adquisición</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Costo</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in maquinaria" :key="item.maquinaria_id"
                                class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.nombre
                                    }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.descripcion }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.tipo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', estadoClase(item.estado)]">
                                        {{ item.estado.charAt(0).toUpperCase() + item.estado.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.fecha_adquisicion
                                    }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ item.costo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="editarMaquinaria(item)"
                                        class="text-blue-600 hover:text-blue-900 mr-4 transition-colors duration-200">Editar</button>
                                    <button @click="eliminarMaquinaria(item.maquinaria_id)"
                                        class="text-red-600 hover:text-red-900 transition-colors duration-200">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal para Crear/Editar Maquinaria -->
            <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div
                    class="bg-white rounded-lg shadow-xl w-full max-w-lg transform transition-all duration-300 ease-in-out">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-2xl font-semibold text-gray-800">{{ maquinariaEdit ? "Editar Maquinaria" :
                            "Nueva Maquinaria" }}</h3>
                    </div>
                    <form @submit.prevent="submitMaquinaria" class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                <input v-model="maquinariaData.nombre" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <textarea v-model="maquinariaData.descripcion"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    rows="3"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                <input v-model="maquinariaData.tipo" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <select v-model="maquinariaData.estado"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="Disponible">Disponible</option>
                                    <option value="En uso">En uso</option>
                                    <option value="En mantenimiento">En mantenimiento</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Adquisición</label>
                                <input v-model="maquinariaData.fecha_adquisicion" type="date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Costo</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                    <input v-model="maquinariaData.costo" type="number" min="0" step="0.01"
                                        class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="cerrarModal"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ maquinariaEdit ? "Actualizar" : "Crear" }}
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
            maquinaria: [],
            maquinariaData: {
                nombre: '',
                descripcion: '',
                tipo: '',
                estado: 'disponible',
                fecha_adquisicion: '',
                costo: 0,
            },
            mostrarModal: false,
            maquinariaEdit: false,
        };
    },
    methods: {
        obtenerMaquinaria() {
            axios.get('http://localhost/GestionConstructora/backend/maquinaria.php')
                .then(response => {
                    this.maquinaria = response.data;
                })
                .catch(error => console.error("Error al obtener maquinaria:", error));
        },
        submitMaquinaria() {
            if (this.maquinariaEdit) {
                this.actualizarMaquinaria();
            } else {
                this.crearMaquinaria();
            }
        },
        crearMaquinaria() {
            axios.post('http://localhost/GestionConstructora/backend/maquinaria.php', this.maquinariaData)
                .then(response => {
                    console.log("Maquinaria creada:", response.data); // Muestra la respuesta en la consola
                    this.maquinaria.push(response.data); // Agrega la maquinaria creada a la lista actual
                    this.cerrarModal(); // Cierra el modal después de crear
                })
                .catch(error => console.error("Error al crear maquinaria:", error));
        },
        editarMaquinaria(maquinaria) {
            this.maquinariaData = { ...maquinaria };
            this.maquinariaEdit = true;
            this.mostrarModal = true;
        },
        actualizarMaquinaria() {
            axios.put('http://localhost/GestionConstructora/backend/maquinaria.php', this.maquinariaData)
                .then(response => {
                    console.log("Maquinaria actualizada:", response.data); // Muestra la respuesta en la consola
                    const index = this.maquinaria.findIndex(item => item.maquinaria_id === this.maquinariaData.maquinaria_id);
                    if (index !== -1) {
                        this.maquinaria[index] = { ...this.maquinariaData }; // Actualiza directamente el elemento en la lista
                    }
                    this.cerrarModal(); // Cierra el modal después de actualizar
                })
                .catch(error => console.error("Error al actualizar maquinaria:", error));
        },
        eliminarMaquinaria(id) {
            axios.delete('http://localhost/GestionConstructora/backend/maquinaria.php', { data: { maquinaria_id: id } })
                .then(response => {
                    console.log("Maquinaria eliminada:", response.data); // Muestra la respuesta en la consola
                    this.maquinaria = this.maquinaria.filter(item => item.maquinaria_id !== id); // Elimina el elemento de la lista
                })
                .catch(error => console.error("Error al eliminar maquinaria:", error));
        },
        abrirModal() {
            this.resetFormulario();
            this.mostrarModal = true;
        },
        cerrarModal() {
            this.mostrarModal = false;
            this.resetFormulario();
        },
        resetFormulario() {
            this.maquinariaData = {
                nombre: '',
                descripcion: '',
                tipo: '',
                estado: 'disponible',
                fecha_adquisicion: '',
                costo: 0,
            };
            this.maquinariaEdit = false;
        },
        estadoClase(estado) {
            switch (estado.toLowerCase()) {
                case 'disponible':
                    return 'bg-green-100 text-green-800';
                case 'en uso':
                    return 'bg-blue-100 text-blue-800';
                case 'en mantenimiento':
                    return 'bg-yellow-100 text-yellow-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }
    },
    mounted() {
        this.obtenerMaquinaria();
    },
};
</script>

<style scoped>
/* Agrega cualquier estilo adicional que necesites aquí */
</style>