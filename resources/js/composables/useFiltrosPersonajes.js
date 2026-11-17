import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

// Búsqueda, filtro y orden del listado, sincronizados con la URL.
//
// Cada cambio pide al servidor sólo los props del listado (only), sin recargar
// la página. La búsqueda espera 300 ms después de la última tecla para no
// consultar por cada letra.

const ORDEN_INICIAL = 'id';
const DIRECCION_INICIAL = 'desc';

export function useFiltrosPersonajes(filtrosDelServidor) {
    const buscar = ref(filtrosDelServidor().buscar);
    const genero = ref(filtrosDelServidor().genero);
    const orden = ref(filtrosDelServidor().orden);
    const direccion = ref(filtrosDelServidor().direccion);
    const cargando = ref(false);

    const hayFiltros = computed(() => buscar.value.trim() !== '' || genero.value !== '');
    const hayAjustes = computed(() => hayFiltros.value
        || orden.value !== ORDEN_INICIAL
        || direccion.value !== DIRECCION_INICIAL);

    let temporizadorBusqueda;
    let numeroPeticion = 0;
    let filtrosEnviados = null;
    let sincronizando = false;

    function filtrosActuales() {
        return {
            buscar: buscar.value.trim(),
            genero: genero.value,
            orden: orden.value,
            direccion: direccion.value,
        };
    }

    // numeroPeticion permite ignorar el fin de una petición vieja si ya salió otra.
    function cargarListado() {
        filtrosEnviados = filtrosActuales();
        const consulta = { orden: filtrosEnviados.orden, direccion: filtrosEnviados.direccion };
        if (filtrosEnviados.buscar) consulta.buscar = filtrosEnviados.buscar;
        if (filtrosEnviados.genero) consulta.genero = filtrosEnviados.genero;

        const estaPeticion = ++numeroPeticion;
        cargando.value = true;

        router.get('/personajes', consulta, {
            only: ['personajes', 'filtros'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
            onFinish: () => {
                if (estaPeticion === numeroPeticion) cargando.value = false;
            },
        });
    }

    function siguioEditando() {
        const actuales = filtrosActuales();

        return Object.keys(actuales).some((clave) => actuales[clave] !== filtrosEnviados[clave]);
    }

    watch(filtrosDelServidor, (filtros) => {
        const respondeALoEnviado = filtrosEnviados
            && Object.keys(filtrosEnviados).every((clave) => filtros[clave] === filtrosEnviados[clave]);

        // No pisar lo que el usuario siguió escribiendo mientras llegaba la respuesta.
        if (respondeALoEnviado && siguioEditando()) return;

        sincronizando = true;
        buscar.value = filtros.buscar;
        genero.value = filtros.genero;
        orden.value = filtros.orden;
        direccion.value = filtros.direccion;
        filtrosEnviados = null;
        nextTick(() => { sincronizando = false; });
    }, { deep: true });

    watch(buscar, () => {
        if (sincronizando) return;
        window.clearTimeout(temporizadorBusqueda);
        temporizadorBusqueda = window.setTimeout(cargarListado, 300);
    });

    watch([genero, orden, direccion], () => {
        if (sincronizando) return;
        window.clearTimeout(temporizadorBusqueda);
        cargarListado();
    });

    onBeforeUnmount(() => {
        window.clearTimeout(temporizadorBusqueda);
        numeroPeticion++;
        filtrosEnviados = null;
    });

    function limpiarFiltros() {
        buscar.value = '';
        genero.value = '';
        orden.value = ORDEN_INICIAL;
        direccion.value = DIRECCION_INICIAL;
    }

    return { buscar, genero, orden, direccion, cargando, hayFiltros, hayAjustes, limpiarFiltros };
}
