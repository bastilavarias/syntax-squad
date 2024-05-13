<script setup lang="ts">
import BaseAppBar from "@/components/BaseAppBar.vue";
</script>

<template>
    <div class="relative">
        <BaseAppBar />
        <slot></slot>
    </div>
</template>
