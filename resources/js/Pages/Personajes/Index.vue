<script setup>
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import EliminarPersonajeModal from '../../Components/EliminarPersonajeModal.vue';
import Paginacion from '../../Components/Paginacion.vue';
import ImportarPersonajeModal from '../../Components/ImportarPersonajeModal.vue';

const page = usePage();
const personajeAEliminar = ref(null);
const importacionAbierta = ref(false);
const tituloArchivo = ref(null);
const cargando = ref(false);
const busqueda = ref('');
const generoSeleccionado = ref('');
const campoOrden = ref('id');
const direccionOrden = ref('desc');
let temporizadorBusqueda;
let numeroPeticion = 0;
let filtrosEnviados = null;
let sincronizando = false;

const props = defineProps({
    personajes: { type: Object, required: true },
    filtros: { type: Object, required: true },
});

busqueda.value = props.filtros.buscar;
generoSeleccionado.value = props.filtros.genero;
campoOrden.value = props.filtros.orden;
direccionOrden.value = props.filtros.direccion;

const generos = { masculino: 'Masculino', femenino: 'Femenino', 'n/a': 'No aplica' };
const hayFiltros = computed(() => busqueda.value.trim() !== '' || generoSeleccionado.value !== '');
const hayAjustes = computed(() => hayFiltros.value
    || campoOrden.value !== 'id'
    || direccionOrden.value !== 'desc');

function cargarArchivo() {
    const query = {
        orden: campoOrden.value,
        direccion: direccionOrden.value,
    };

    if (busqueda.value.trim()) query.buscar = busqueda.value.trim();
    if (generoSeleccionado.value) query.genero = generoSeleccionado.value;

    filtrosEnviados = {
        buscar: busqueda.value.trim(),
        genero: generoSeleccionado.value,
        orden: campoOrden.value,
        direccion: direccionOrden.value,
    };
    const estaPeticion = ++numeroPeticion;
    cargando.value = true;

    router.get('/personajes', query, {
        only: ['personajes', 'filtros'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
        onFinish: () => {
            if (estaPeticion === numeroPeticion) cargando.value = false;
        },
    });
}

watch(() => props.filtros, (filtros) => {
    const coincideConLaUltimaConsulta = filtrosEnviados
        && Object.keys(filtrosEnviados).every((clave) => filtros[clave] === filtrosEnviados[clave]);
    const huboEdicionPosterior = filtrosEnviados
        && (busqueda.value.trim() !== filtrosEnviados.buscar
            || generoSeleccionado.value !== filtrosEnviados.genero
            || campoOrden.value !== filtrosEnviados.orden
            || direccionOrden.value !== filtrosEnviados.direccion);

    // No pisar lo que se siguió escribiendo mientras llegaba una respuesta anterior.
    if (coincideConLaUltimaConsulta && huboEdicionPosterior) return;

    sincronizando = true;
    busqueda.value = filtros.buscar;
    generoSeleccionado.value = filtros.genero;
    campoOrden.value = filtros.orden;
    direccionOrden.value = filtros.direccion;
    filtrosEnviados = null;
    nextTick(() => { sincronizando = false; });
}, { deep: true });

watch(busqueda, () => {
    if (sincronizando) return;
    window.clearTimeout(temporizadorBusqueda);
    temporizadorBusqueda = window.setTimeout(cargarArchivo, 300);
});

watch([generoSeleccionado, campoOrden, direccionOrden], () => {
    if (sincronizando) return;
    window.clearTimeout(temporizadorBusqueda);
    cargarArchivo();
});

function limpiarFiltros() {
    busqueda.value = '';
    generoSeleccionado.value = '';
    campoOrden.value = 'id';
    direccionOrden.value = 'desc';
}

function cambiarDireccion() {
    direccionOrden.value = direccionOrden.value === 'asc' ? 'desc' : 'asc';
}

onBeforeUnmount(() => {
    window.clearTimeout(temporizadorBusqueda);
    numeroPeticion++;
    filtrosEnviados = null;
});

async function operacionCompletada() {
    personajeAEliminar.value = null;
    importacionAbierta.value = false;
    await nextTick();
    tituloArchivo.value?.focus();
}
</script>

<template>
    <Head title="Archivo de personajes · Star Wars" />
    <main id="contenido" class="page-shell">
        <header class="saga-hero">
            <div class="hero-copy">
                <p class="eyebrow"><span aria-hidden="true"></span> El universo Star Wars</p>
                <h1 class="page-title">Personajes<br>de la <em>galaxia.</em></h1>
                <p class="page-description">Cada personaje tiene una historia.<br>Explorá sus fichas y construí tu propio archivo.</p>
            </div>
        </header>

        <p v-if="page.flash?.exito" class="notice-success" role="status">{{ page.flash.exito }}</p>

        <section aria-labelledby="archivo-titulo" class="collection" :aria-busy="cargando">
            <div class="collection-bar">
                <div class="collection-heading">
                    <h2 id="archivo-titulo" ref="tituloArchivo" tabindex="-1">Tu archivo</h2>
                    <span class="count-badge" aria-live="polite">{{ personajes.total }} {{ personajes.total === 1 ? 'personaje' : 'personajes' }}</span>
                </div>
                <div class="collection-actions">
                    <button type="button" class="btn-secondary" @click="importacionAbierta = true">Importar desde SWAPI</button>
                    <Link href="/personajes/crear" class="btn-primary"><span aria-hidden="true">＋</span> Crear personaje</Link>
                </div>
            </div>

            <div class="archive-tools" role="group" aria-label="Buscar, filtrar y ordenar el archivo">
                <div class="archive-filter archive-filter-wide">
                    <label for="buscar-personaje">Buscar por nombre</label>
                    <input
                        id="buscar-personaje"
                        v-model="busqueda"
                        class="form-control"
                        type="search"
                        maxlength="100"
                        autocomplete="off"
                        placeholder="Ej.: Leia Organa"
                    >
                </div>
                <div class="archive-filter">
                    <label for="filtrar-genero">Género</label>
                    <select id="filtrar-genero" v-model="generoSeleccionado" class="form-control">
                        <option value="">Todos</option>
                        <option value="masculino">Masculino</option>
                        <option value="femenino">Femenino</option>
                        <option value="n/a">No aplica</option>
                    </select>
                </div>
                <div class="archive-filter">
                    <label for="ordenar-personajes">Ordenar por</label>
                    <select id="ordenar-personajes" v-model="campoOrden" class="form-control">
                        <option value="id">N.º de ficha</option>
                        <option value="nombre">Nombre</option>
                        <option value="altura">Altura</option>
                        <option value="masa">Masa</option>
                        <option value="genero">Género</option>
                    </select>
                </div>
                <div class="archive-filter archive-filter-actions">
                    <span class="archive-filter-label">Dirección</span>
                    <div class="filter-buttons">
                        <button
                            type="button"
                            class="btn-secondary sort-direction"
                            :aria-label="'Cambiar a orden ' + (direccionOrden === 'asc' ? 'descendente' : 'ascendente')"
                            @click="cambiarDireccion"
                        >
                            <span aria-hidden="true">{{ direccionOrden === 'asc' ? '↑' : '↓' }}</span>
                            {{ direccionOrden === 'asc' ? 'Ascendente' : 'Descendente' }}
                        </button>
                        <button type="button" class="btn-secondary clear-filters" :disabled="!hayAjustes" @click="limpiarFiltros">
                            Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <p class="filter-status" role="status" aria-live="polite">
                {{ cargando ? 'Actualizando el archivo…' : 'Se muestran ' + personajes.total + (personajes.total === 1 ? ' personaje' : ' personajes') }}
            </p>

            <ul v-if="personajes.data.length" class="character-grid">
                <li v-for="personaje in personajes.data" :key="personaje.id" class="character-card">
                    <div class="card-topline">
                        <span class="record-id">Ficha / {{ String(personaje.id).padStart(3, '0') }}</span>
                        <span class="origin-badge" :class="{ 'origin-swapi': personaje.origen_swapi }">{{ personaje.origen_swapi ? 'SWAPI' : 'Manual' }}</span>
                    </div>
                    <h3 class="card-heading">{{ personaje.nombre }}</h3>
                    <dl class="card-data">
                        <div>
                            <dt>Altura</dt>
                            <dd>{{ personaje.altura ?? '—' }} <span v-if="personaje.altura != null">cm</span><span v-else class="sr-only">Sin datos</span></dd>
                        </div>
                        <div>
                            <dt>Masa</dt>
                            <dd>{{ personaje.masa ?? '—' }} <span v-if="personaje.masa != null">kg</span><span v-else class="sr-only">Sin datos</span></dd>
                        </div>
                        <div class="gender-data">
                            <dt>Género</dt>
                            <dd>{{ generos[personaje.genero] ?? personaje.genero }}</dd>
                        </div>
                    </dl>
                    <div class="card-actions">
                        <Link :href="'/personajes/' + personaje.id + '/editar'" :aria-label="'Editar ficha de ' + personaje.nombre" class="card-action">
                            Editar ficha <span aria-hidden="true">↗</span>
                        </Link>
                        <button type="button" class="card-delete" :aria-label="'Eliminar a ' + personaje.nombre" @click="personajeAEliminar = personaje">
                            Eliminar
                        </button>
                    </div>
                </li>
            </ul>
            <div v-else class="empty-state">
                <h3>{{ hayFiltros ? 'No encontramos coincidencias.' : 'El comienzo de una nueva historia.' }}</h3>
                <p v-if="hayFiltros">Probá cambiar el nombre o el género, o limpiá los filtros para ver todo el archivo.</p>
                <p v-else>Creá tu primer personaje o importalo desde SWAPI para empezar el archivo.</p>
            </div>
            <Paginacion :paginador="personajes" />
        </section>
        <ImportarPersonajeModal
            :abierto="importacionAbierta"
            @cerrar="importacionAbierta = false"
            @importado="operacionCompletada"
        />
        <EliminarPersonajeModal
            :personaje="personajeAEliminar"
            @cerrar="personajeAEliminar = null"
            @eliminado="operacionCompletada"
        />
    </main>
</template>
