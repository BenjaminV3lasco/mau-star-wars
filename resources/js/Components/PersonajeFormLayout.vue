<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ titulo: { type: String, required: true }, descripcion: { type: String, required: true } });
</script>

<template>
    <main id="contenido" class="page-shell form-page">
        <Link href="/personajes" class="back-link"><span aria-hidden="true">←</span> Volver al archivo</Link>
        <div class="form-layout">
            <header class="form-intro">
                <div class="form-intro-copy">
                    <p class="eyebrow">Star Wars · Archivo</p>
                    <h1 class="page-title">{{ titulo }}</h1>
                    <p class="page-description">{{ descripcion }}</p>
                </div>
            </header>
            <slot />
        </div>
    </main>
</template>
