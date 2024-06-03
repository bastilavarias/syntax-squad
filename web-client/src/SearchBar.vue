<template>
    <form class="flex w-full" @submit="$emit('go-search', $event)">
        <Input
            type="search"
            placeholder="Search..."
            class="w-full"
            v-model="search"
        />
    </form>

</template>

<script setup lang="ts">
const search = defineModel({ required: true });
import { Input } from "@/components/ui/input";
</script>

<style scoped>

</style>