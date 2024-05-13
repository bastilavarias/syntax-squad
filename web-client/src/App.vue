<script setup lang="ts">
import MainLayout from "@/layouts/MainLayout.vue";
import Toaster from "@/components/ui/toast/Toaster.vue";
</script>

<template>
    <main-layout>
        <RouterView />
        <Toaster />
    </main-layout>
</template>
