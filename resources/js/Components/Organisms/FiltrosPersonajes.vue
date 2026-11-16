<script setup>
import Boton from '../Atoms/Boton.vue';
import Entrada from '../Atoms/Entrada.vue';
import Selector from '../Atoms/Selector.vue';
import { GENEROS } from '../../utils/personaje.js';

defineProps({ hayAjustes: { type: Boolean, default: false } });
defineEmits(['limpiar']);

const buscar = defineModel('buscar', { type: String, required: true });
const genero = defineModel('genero', { type: String, required: true });
const orden = defineModel('orden', { type: String, required: true });
const direccion = defineModel('direccion', { type: String, required: true });

const CAMPOS_ORDEN = [
    { valor: 'id', etiqueta: 'N.º de ficha' },
    { valor: 'nombre', etiqueta: 'Nombre' },
    { valor: 'altura', etiqueta: 'Altura' },
    { valor: 'masa', etiqueta: 'Masa' },
    { valor: 'genero', etiqueta: 'Género' },
];
const CLASES_ETIQUETA = 'text-[.67rem] font-medium tracking-[.08em] text-muted uppercase';

function cambiarDireccion() {
    direccion.value = direccion.value === 'asc' ? 'desc' : 'asc';
}
</script>

<template>
    <div
        class="mb-3.5 grid grid-cols-1 items-end gap-3.5 xs:grid-cols-2 md:grid-cols-[minmax(15rem,1.25fr)_minmax(9rem,.7fr)_minmax(9rem,.7fr)_auto]"
        role="group"
        aria-label="Buscar, filtrar y ordenar el archivo"
    >
        <div class="grid min-w-0 gap-2 xs:col-span-2 md:col-span-1">
            <label for="buscar-personaje" :class="CLASES_ETIQUETA">Buscar por nombre</label>
            <Entrada id="buscar-personaje" v-model="buscar" type="search" maxlength="100" autocomplete="off" placeholder="Ej.: Leia Organa" />
        </div>

        <div class="grid min-w-0 gap-2">
            <label for="filtrar-genero" :class="CLASES_ETIQUETA">Género</label>
            <Selector id="filtrar-genero" v-model="genero">
                <option value="">Todos</option>
                <option v-for="opcion in GENEROS" :key="opcion.valor" :value="opcion.valor">{{ opcion.etiqueta }}</option>
            </Selector>
        </div>

        <div class="grid min-w-0 gap-2">
            <label for="ordenar-personajes" :class="CLASES_ETIQUETA">Ordenar por</label>
            <Selector id="ordenar-personajes" v-model="orden">
                <option v-for="campo in CAMPOS_ORDEN" :key="campo.valor" :value="campo.valor">{{ campo.etiqueta }}</option>
            </Selector>
        </div>

        <div class="grid min-w-0 gap-2">
            <span :class="CLASES_ETIQUETA">Dirección</span>
            <div class="flex gap-2 *:flex-1 xs:*:flex-none">
                <Boton
                    variante="secundario"
                    tamano="compacto"
                    :aria-label="`Cambiar a orden ${direccion === 'asc' ? 'descendente' : 'ascendente'}`"
                    @click="cambiarDireccion"
                >
                    <span class="text-base text-gold" aria-hidden="true">{{ direccion === 'asc' ? '↑' : '↓' }}</span>
                    {{ direccion === 'asc' ? 'Ascendente' : 'Descendente' }}
                </Boton>
                <Boton variante="secundario" tamano="compacto" :disabled="!hayAjustes" @click="$emit('limpiar')">Limpiar</Boton>
            </div>
        </div>
    </div>
</template>
