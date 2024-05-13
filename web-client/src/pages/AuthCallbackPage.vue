<script setup lang="ts">
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth.ts";
import { computed, onMounted, ref } from "vue";
import { useToast } from "@/components/ui/toast";

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const { toast } = useToast();

const loading = ref(false);
const error = ref(null);

const onLogin = async () => {
    loading.value = true;
    const payload = {
        code: route.query.code,
    };
    const result = await authStore.login(payload);
    if (result.success) {
        toast({
            title: "Account login completed.",
            description:
                "Congratulations! Your account credentials are valid. You will be redirected to the home page shortly.",
        });
        await router.push({ name: "home-page" });

        return;
    }
    loading.value = false;
    error.value = result.message;
};

onLogin();
</script>

<template>
    <div class="h-screen flex flex-col justify-center items-center">
        <div class="flex flex-col items-center">
            <img class="w-auto h-40" src="/nyan-cat.gif" alt="Auth GIF" />
        </div>
    </div>
</template>
