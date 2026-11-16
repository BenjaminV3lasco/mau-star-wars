<script setup>
import { onBeforeUnmount, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ personaje: { type: Object, default: null } });
const emit = defineEmits(['cerrar', 'eliminado']);
const dialogo = ref(null);
const form = useForm({});

watch(() => props.personaje, (personaje) => {
    if (personaje) {
        form.clearErrors();
        if (!dialogo.value.open) dialogo.value.showModal();
    } else if (dialogo.value.open) {
        dialogo.value.close();
    }
}, { flush: 'post' });

onBeforeUnmount(() => dialogo.value?.close());

function cerrar() {
    if (!form.processing) emit('cerrar');
}

function eliminar() {
    if (!props.personaje || form.processing) return;

    form.clearErrors();
    form.delete('/personajes/' + props.personaje.id, {
        onSuccess: () => emit('eliminado'),
        onHttpException: (response) => {
            const mensajes = {
                404: 'Este personaje ya no existe. Cerrá este aviso y recargá el listado.',
                419: 'La sesión venció. Recargá la página e intentá nuevamente.',
            };
            form.setError('eliminar', mensajes[response.status] ?? 'No pudimos completar la eliminación. Intentá nuevamente.');
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
    <dialog
        ref="dialogo"
        class="confirm-dialog"
        aria-labelledby="eliminar-titulo"
        aria-describedby="eliminar-descripcion"
        @cancel.prevent="cerrar"
    >
        <form v-if="personaje" :aria-busy="form.processing" @submit.prevent="eliminar">
            <p class="eyebrow">Archivo de personajes</p>
            <h2 id="eliminar-titulo" class="modal-title">Eliminar personaje</h2>
            <p id="eliminar-descripcion" class="modal-description">
                Vas a eliminar a <strong>{{ personaje.nombre }}</strong> del archivo.
                Esta acción no se puede deshacer.
            </p>
            <p v-if="form.errors.eliminar" class="notice-error" role="alert">{{ form.errors.eliminar }}</p>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" :disabled="form.processing" autofocus @click="cerrar">Cancelar</button>
                <button type="submit" class="btn-danger" :disabled="form.processing">
                    {{ form.processing ? 'Eliminando…' : 'Eliminar personaje' }}
                </button>
            </div>
            <p v-if="form.processing" class="sr-only" role="status">Eliminando personaje. Esperá un momento.</p>
        </form>
    </dialog>
</template>
