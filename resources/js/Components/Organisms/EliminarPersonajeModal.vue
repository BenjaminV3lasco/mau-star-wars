<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Aviso from '../Atoms/Aviso.vue';
import ModalDialogo from '../Molecules/ModalDialogo.vue';

const props = defineProps({ personaje: { type: Object, default: null } });
const emit = defineEmits(['cerrar', 'eliminado']);

const form = useForm({});

watch(() => props.personaje, () => form.clearErrors());

const MENSAJES_HTTP = {
    404: 'Este personaje ya no existe. Cerrá este aviso y recargá el listado.',
    419: 'La sesión venció. Recargá la página e intentá nuevamente.',
};

function eliminar() {
    if (!props.personaje || form.processing) return;

    form.clearErrors();
    form.delete(`/personajes/${props.personaje.id}`, {
        onSuccess: () => emit('eliminado'),
        onHttpException: (respuesta) => {
            form.setError('eliminar', MENSAJES_HTTP[respuesta.status] ?? 'No pudimos completar la eliminación. Intentá nuevamente.');
            return false;
        },
        onNetworkError: () => {
            form.setError('eliminar', 'No pudimos conectar con el servidor. Revisá la conexión e intentá nuevamente.');
            return false;
        },
    });
}
</script>

<template>
    <ModalDialogo
        :abierto="Boolean(personaje)"
        :ocupado="form.processing"
        antetitulo="Archivo de personajes"
        titulo="Eliminar personaje"
        texto-confirmar="Eliminar personaje"
        texto-ocupado="Eliminando…"
        variante="peligro"
        @cerrar="emit('cerrar')"
        @confirmar="eliminar"
    >
        <template #descripcion>
            Vas a eliminar a <strong class="font-semibold text-strong">{{ personaje?.nombre }}</strong> del archivo.
            Esta acción no se puede deshacer.
        </template>

        <Aviso v-if="form.errors.eliminar" tipo="error" class="mt-4">{{ form.errors.eliminar }}</Aviso>
    </ModalDialogo>
</template>
