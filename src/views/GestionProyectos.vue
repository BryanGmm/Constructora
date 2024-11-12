<template>
  <div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Gestión de Proyectos</h1>
        <button @click="abrirModalCrear" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
          Crear Proyecto
        </button>
      </div>

      <!-- Tabla de Proyectos -->
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avance</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Inicio</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inversión</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="proyecto in proyectos"
              :key="proyecto.proyecto_id"
              class="hover:bg-gray-100 transition duration-150 cursor-pointer"
              @click="verDetalles(proyecto)"
            >
              <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ proyecto.nombre }}</td>
              <td class="px-6 py-4 text-sm">
                <span :class="badgeClass(proyecto.estado)" class="px-2 py-1 rounded-full">{{ estados[proyecto.estado] }}</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ proyecto.porcentaje_avance }}%</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ proyecto.fecha_inicio }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ formatCurrency(proyecto.inversion_inicial) }}</td>
              <td class="px-6 py-4 text-sm font-medium space-x-2">
                <button @click.stop="editarProyecto(proyecto)" class="text-blue-600 hover:text-blue-900">Editar</button>
                <button @click.stop="eliminarProyecto(proyecto.proyecto_id)" class="text-red-600 hover:text-red-900">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal Crear/Editar Proyecto -->
      <div v-if="mostrarModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-2xl">
          <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-2xl font-semibold text-gray-800">{{ proyectoEdit ? "Editar Proyecto" : "Nuevo Proyecto" }}</h2>
          </div>
          <div class="px-6 py-4 max-h-[calc(100vh-200px)] overflow-y-auto">
            <form @submit.prevent="submitProyecto" class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="col-span-2 space-y-2">
                <label class="block text-sm font-medium text-gray-700">Nombre del Proyecto</label>
                <input v-model="proyecto.nombre" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" required />
              </div>
              <div class="col-span-2 space-y-2">
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea v-model="proyecto.descripcion" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"></textarea>
              </div>
              <div class="col-span-2 space-y-2">
                <label class="block text-sm font-medium text-gray-700">Imagen</label>
                <input type="file" @change="onFileChange" class="w-full" />
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Ubicación</label>
                <input v-model="proyecto.ubicacion" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" />
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                <input v-model="proyecto.fecha_inicio" type="date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" />
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
                <input v-model="proyecto.fecha_fin" type="date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" />
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <select v-model="proyecto.estado" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
                  <option value="NI">No Iniciado</option>
                  <option value="EE">En Ejecución</option>
                  <option value="PF">Proyecto Finalizado</option>
                </select>
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Porcentaje de Avance</label>
                <input v-model="proyecto.porcentaje_avance" type="number" min="0" max="100" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" />
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Inversión Inicial</label>
                <input v-model="proyecto.inversion_inicial" type="number" min="0" step="0.01" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" />
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Inversión Final</label>
                <input v-model="proyecto.inversion_final" type="number" min="0" step="0.01" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" />
              </div>
            </form>
          </div>
          <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end space-x-3">
            <button type="button" @click="cancelarEdicion" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
            <button type="submit" @click="submitProyecto" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">{{ proyectoEdit ? "Actualizar Proyecto" : "Crear Proyecto" }}</button>
          </div>
        </div>
      </div>

      <!-- Modal de Detalles del Proyecto -->
      <div v-if="mostrarDetalles" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-2xl">
          <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-semibold text-gray-800">Detalles del Proyecto</h2>
            <button @click="cerrarDetalles" class="text-gray-500 hover:text-gray-800">×</button>
          </div>
          <div class="px-6 py-4 max-h-[calc(100vh-200px)] overflow-y-auto">
            <img v-if="proyectoSeleccionado.imagen" :src="proyectoSeleccionado.imagen" alt="Imagen del proyecto" class="w-full h-64 object-cover rounded-lg mb-4">
            <div v-else class="w-full h-64 bg-gray-200 flex items-center justify-center text-gray-500 mb-4">
              <span>Sin Imagen</span>
            </div>
            <p class="text-gray-600 mb-2"><strong>Nombre:</strong> {{ proyectoSeleccionado.nombre }}</p>
            <p class="text-gray-600 mb-2"><strong>Descripción:</strong> {{ proyectoSeleccionado.descripcion }}</p>
            <p class="text-gray-600 mb-2"><strong>Ubicación:</strong> {{ proyectoSeleccionado.ubicacion }}</p>
            <p class="text-gray-600 mb-2"><strong>Fecha de Inicio:</strong> {{ proyectoSeleccionado.fecha_inicio }}</p>
            <p class="text-gray-600 mb-2"><strong>Fecha de Fin:</strong> {{ proyectoSeleccionado.fecha_fin }}</p>
            <p class="text-gray-600 mb-2"><strong>Avance:</strong> {{ proyectoSeleccionado.porcentaje_avance }}%</p>
            <p class="text-gray-600 mb-2"><strong>Inversión Inicial:</strong> {{ formatCurrency(proyectoSeleccionado.inversion_inicial) }}</p>
            <p class="text-gray-600 mb-2"><strong>Inversión Final:</strong> {{ formatCurrency(proyectoSeleccionado.inversion_final) }}</p>
            <p class="text-gray-600"><strong>Estado:</strong> <span :class="badgeClass(proyectoSeleccionado.estado)" class="px-2 py-1 rounded-full">{{ estados[proyectoSeleccionado.estado] }}</span></p>
          </div>
          <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end">
            <button @click="cerrarDetalles" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      proyectos: [],
      proyecto: {
        nombre: "",
        descripcion: "",
        ubicacion: "",
        fecha_inicio: "",
        fecha_fin: "",
        estado: "NI",
        porcentaje_avance: 0,
        inversion_inicial: 0,
        inversion_final: 0,
        imagen: ""
      },
      proyectoSeleccionado: {},
      estados: {
        NI: "No Iniciado",
        EE: "En Ejecución",
        PF: "Proyecto Finalizado"
      },
      proyectoEdit: false,
      mostrarModal: false,
      mostrarDetalles: false
    };
  },
  methods: {
    obtenerProyectos() {
      axios.get("http://localhost/GestionConstructora/backend/proyectos.php")
        .then(response => {
          if (Array.isArray(response.data)) {
            this.proyectos = response.data;
          } else {
            console.error("Error: Se esperaba un array en response.data, pero se recibió:", response.data);
          }
        })
        .catch(error => console.error("Error al obtener proyectos:", error));
    },
    abrirModalCrear() {
      this.resetProyecto();
      this.proyectoEdit = false;
      this.mostrarModal = true;
    },
    verDetalles(proyecto) {
      this.proyectoSeleccionado = proyecto;
      this.mostrarDetalles = true;
    },
    cerrarDetalles() {
      this.mostrarDetalles = false;
      this.proyectoSeleccionado = {};
    },
    onFileChange(event) {
      const file = event.target.files[0];
      const reader = new FileReader();
      reader.onload = (e) => {
        this.proyecto.imagen = e.target.result.split(",")[1];
      };
      reader.readAsDataURL(file);
    },
    submitProyecto() {
      if (this.proyectoEdit) {
        this.actualizarProyecto();
      } else {
        this.crearProyecto();
      }
    },
    crearProyecto() {
      axios.post("http://localhost/GestionConstructora/backend/proyectos.php", this.proyecto)
        .then(response => {
          console.log("Proyecto creado:", response.data);
          this.obtenerProyectos();
          this.cancelarEdicion();
        })
        .catch(error => console.error("Error al crear proyecto:", error));
    },
    editarProyecto(proyecto) {
      this.proyecto = { ...proyecto, imagen: "" };
      this.proyectoEdit = true;
      this.mostrarModal = true;
    },
    actualizarProyecto() {
      const datosProyecto = { ...this.proyecto };
      if (!datosProyecto.imagen) delete datosProyecto.imagen;
      axios.put("http://localhost/GestionConstructora/backend/proyectos.php", datosProyecto)
        .then(response => {
          console.log("Proyecto actualizado:", response.data);
          this.obtenerProyectos();
          this.cancelarEdicion();
        })
        .catch(error => console.error("Error al actualizar proyecto:", error));
    },
    eliminarProyecto(id) {
      if (confirm("¿Estás seguro de que deseas eliminar este proyecto?")) {
        axios.delete("http://localhost/GestionConstructora/backend/proyectos.php", { data: { proyecto_id: id } })
          .then(response => {
            console.log("Proyecto eliminado:", response.data);
            this.obtenerProyectos();
          })
          .catch(error => console.error("Error al eliminar proyecto:", error));
      }
    },
    cancelarEdicion() {
      this.resetProyecto();
      this.mostrarModal = false;
    },
    resetProyecto() {
      this.proyecto = {
        nombre: "",
        descripcion: "",
        ubicacion: "",
        fecha_inicio: "",
        fecha_fin: "",
        estado: "NI",
        porcentaje_avance: 0,
        inversion_inicial: 0,
        inversion_final: 0,
        imagen: ""
      };
    },
    formatCurrency(value) {
      return new Intl.NumberFormat("es-ES", { style: "currency", currency: "USD" }).format(value);
    },
    badgeClass(estado) {
      return estado === "NI"
        ? "bg-gray-200 text-gray-800"
        : estado === "EE"
          ? "bg-yellow-200 text-yellow-800"
          : "bg-green-200 text-green-800";
    }
  },
  created() {
    this.obtenerProyectos();
  }
};
</script>
