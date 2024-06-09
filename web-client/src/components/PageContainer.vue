<script setup lang="ts">
import { cn } from "@/lib/utils.ts";

const props = defineProps(["class"]);
</script>

<template>
    <div
        :class="
            cn([
                props.class,
                'h-screen md:px-4 md:container md:pt-8 md:pb-8 bg-green-600',
            ])
        "
    >
        <slot />
    </div>
</template>
