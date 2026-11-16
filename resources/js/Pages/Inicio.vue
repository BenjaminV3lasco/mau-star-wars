<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>
<template>
    <Head title="Archivo galáctico · Star Wars" />
    <main id="contenido" class="page-shell landing-shell">
        <section class="saga-hero landing">
            <div class="hero-copy">
                <p class="eyebrow"><span aria-hidden="true"></span> Una galaxia muy, muy lejana…</p>
                <h1 class="page-title">Grandes historias.<br><em>Personajes eternos.</em></h1>
                <p class="page-description">El universo de Star Wars, personaje por personaje.<br>Creá tu archivo y descubrí a sus protagonistas.</p>
                <Link href="/personajes" class="btn-primary">Explorar personajes <span aria-hidden="true">→</span></Link>
            </div>
        </section>
    </main>
</template>
