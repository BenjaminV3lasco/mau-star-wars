<script setup>
import Boton from '../Atoms/Boton.vue';
import Entrada from '../Atoms/Entrada.vue';
import Selector from '../Atoms/Selector.vue';
import CampoFormulario from '../Molecules/CampoFormulario.vue';
import { GENEROS } from '../../utils/personaje.js';

defineProps({
    form: { type: Object, required: true },
    textoEnviar: { type: String, required: true },
});
defineEmits(['enviar']);

const SECCIONES = [
    {
        titulo: 'Identidad',
        campos: [
            { nombre: 'nombre', etiqueta: 'Nombre', tipo: 'text', requerido: true, ancho: true, minlength: 3, maxlength: 100, ejemplo: 'Ej. Leia Organa' },
            { nombre: 'genero', etiqueta: 'Género', tipo: 'select', requerido: true },
            { nombre: 'planeta_origen', etiqueta: 'Planeta de origen', tipo: 'text', maxlength: 100, ejemplo: 'Ej. Alderaan' },
        ],
    },
    {
        titulo: 'Características',
        campos: [
            { nombre: 'altura', etiqueta: 'Altura', tipo: 'number', min: 1, max: 300, unidad: 'cm', ejemplo: 'Ej. 150' },
            { nombre: 'masa', etiqueta: 'Masa', tipo: 'number', min: 1, max: 500, unidad: 'kg', ejemplo: 'Ej. 49' },
            { nombre: 'color_cabello', etiqueta: 'Color de cabello', tipo: 'text', maxlength: 100, ejemplo: 'Ej. Castaño' },
            { nombre: 'color_ojos', etiqueta: 'Color de ojos', tipo: 'text', maxlength: 100, ejemplo: 'Ej. Marrón' },
        ],
    },
];
</script>

<template>
    <form
        class="min-w-0 animate-entrar rounded-[3px] border border-[#363941] bg-linear-135 from-[#191c21] to-[#101215] p-5 xs:p-6 md:p-8
            light:border-[#d8d3c7] light:from-panel light:to-[#f7f5ee]"
        :aria-busy="form.processing"
        @submit.prevent="$emit('enviar')"
    >
        <p class="pb-6 text-xs text-[#aeb2bb] light:text-[#3f444d]">
            Los campos con <span class="text-gold" aria-hidden="true">*</span> son obligatorios.
        </p>

        <fieldset
            v-for="(seccion, indice) in SECCIONES"
            :key="seccion.titulo"
            class="mb-7 min-w-0 disabled:opacity-60"
            :disabled="form.processing"
        >
            <legend class="float-left mb-[1.1rem] flex w-full items-center gap-2.5 font-display text-xl font-semibold tracking-[.04em] uppercase">
                <span class="inline-flex size-6 items-center justify-center rounded-[2px] border border-[#57513a] font-sans text-[.61rem] tracking-normal text-gold" aria-hidden="true">
                    0{{ indice + 1 }}
                </span>
                {{ seccion.titulo }}
            </legend>

            <div class="clear-both grid grid-cols-1 gap-x-5 gap-y-[1.15rem] xs:grid-cols-2">
                <CampoFormulario
                    v-for="campo in seccion.campos"
                    :id="campo.nombre"
                    :key="campo.nombre"
                    v-slot="{ invalido, descripcion }"
                    :etiqueta="campo.etiqueta"
                    :requerido="campo.requerido"
                    :unidad="campo.unidad"
                    :error="form.errors[campo.nombre]"
                    :class="{ 'xs:col-span-2': campo.ancho }"
                >
                    <Selector
                        v-if="campo.tipo === 'select'"
                        :id="campo.nombre"
                        v-model="form[campo.nombre]"
                        :name="campo.nombre"
                        required
                        :aria-invalid="invalido"
                        :aria-describedby="descripcion"
                    >
                        <option value="" disabled>Seleccioná una opción</option>
                        <option v-for="genero in GENEROS" :key="genero.valor" :value="genero.valor">{{ genero.etiqueta }}</option>
                    </Selector>
                    <Entrada
                        v-else
                        :id="campo.nombre"
                        v-model="form[campo.nombre]"
                        :name="campo.nombre"
                        :type="campo.tipo"
                        :required="campo.requerido"
                        :minlength="campo.minlength"
                        :maxlength="campo.maxlength"
                        :min="campo.min"
                        :max="campo.max"
                        :step="campo.tipo === 'number' ? 1 : undefined"
                        :placeholder="campo.ejemplo"
                        :aria-invalid="invalido"
                        :aria-describedby="descripcion"
                        :class="{ 'pr-12': campo.unidad }"
                    />
                </CampoFormulario>
            </div>
        </fieldset>

        <div class="flex flex-col gap-3 border-t border-line pt-6 xs:flex-row xs:flex-wrap">
            <Boton tipo="submit" :disabled="form.processing" :cargando="form.processing">
                {{ form.processing ? 'Guardando…' : textoEnviar }}
                <span v-if="!form.processing" class="text-lg" aria-hidden="true">↗</span>
            </Boton>
            <Boton href="/personajes" variante="secundario">Cancelar</Boton>
        </div>
    </form>
</template>
