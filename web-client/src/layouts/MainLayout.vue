<script setup lang="ts">
import BaseAppBar from "@/components/BaseAppBar.vue";
import BaseSidebar from "@/components/BaseSidebar.vue";
</script>

<template>
    <div class="relative pt-14">
        <BaseAppBar />
        <slot></slot>
    </div>
</template>
