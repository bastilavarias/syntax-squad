<template>
    <Dialog>
        <DialogTrigger>
            <Button class="block lg:hidden" variant="outline">
                <Menu />
            </Button>
        </DialogTrigger>
        <DialogContent class="h-screen w-[300px] translate-x-0 left-0 !rounded-none">
            <CustomSideBar />
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Dialog, DialogContent, DialogTrigger } from "@/components/ui/dialog";
import { ref } from "vue";
import CustomSideBar from "@/components/CustomSideBar.vue";
import { Menu } from "lucide-vue-next";
import { Button } from "@/components/ui/button";

const drawer = ref(true);
</script>

<style scoped>

</style>