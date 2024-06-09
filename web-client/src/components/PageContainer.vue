<script setup lang="ts">
import { cn } from "@/lib/utils.ts";

const props = defineProps(["class"]);
</script>

<template>
    <main
        :class="
            cn([
                props.class,
                'h-[calc(100vh-3.5rem)] md:px-4 md:container md:pt-8 md:pb-8',
            ])
        "
    >
        <slot />
    </main>
</template>
