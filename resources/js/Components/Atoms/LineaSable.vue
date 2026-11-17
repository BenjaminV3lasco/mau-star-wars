<script setup>
defineProps({
    hielo: { type: Boolean, default: false },
    arriba: { type: Boolean, default: false },
});
</script>

<template>
    <span
        class="pointer-events-none absolute left-0 h-0.5 w-32 origin-left animate-encender-sable rounded-full"
        :class="[
            arriba ? '-top-px' : '-bottom-px',
            hielo
                ? 'bg-ice shadow-[0_0_10px_#94d9ed,0_0_22px_#94d9ed80] light:shadow-[0_0_8px_#08718a66]'
                : 'bg-gold shadow-[0_0_10px_#ffe15a,0_0_22px_#ffe15a80] light:shadow-[0_0_8px_#80600066]',
        ]"
        aria-hidden="true"
    ></span>
</template>
