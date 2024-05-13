<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { useRoute, useRouter } from "vue-router";
import { PlusIcon, GithubIcon } from "lucide-vue-next";
import { Input } from "@/components/ui/input";
import BaseUserMenuDropdown from "@/components/BaseUserMenuDropdown.vue";
import CustomLoadingSpinner from "@/components/CustomLoadingSpinner.vue";
import { computed, ref, watch } from "vue";
import { useAuthStore } from "@/stores/auth.ts";

const router = useRouter();
const authStore = useAuthStore();
const route = useRoute();

const loading = ref(false);
const keyword = ref(null);

const showLoginButton = computed(() => route.name !== "auth-callback-page");

watch(
    () => route.query.keyword,
    (value) => {
        if (value) {
            keyword.value = value;
        }
    }
);

const goToSearchPage = (e) => {
    e.preventDefault();
    if (keyword.value) {
        router.push({ name: "search-page", query: { keyword: keyword.value } });
    }
};
const onOpenGithub0auth = () => {
    loading.value = true;
    const clientID = import.meta.env.VITE_GITHUB_OAUTH_CLIENT_ID;
    window.location.href = `https://github.com/login/oauth/authorize?client_id=${clientID}`;
};

if (route.query.keyword) {
    keyword.value = route.query.keyword;
}
</script>

<template>
    <header
        class="sticky z-40 top-0 bg-background/80 backdrop-blur-lg border-b border-border"
    >
        <div class="container flex justify-between h-14 items-center">
            <nav class="flex items-center space-x-2">
                <router-link :to="{ name: 'home-page' }">
                    <span
                        class="text-md bg-black font-black text-white decoration-0 tracking-tight py-2 px-2 rounded"
                    >
                        SyntaxSquad
                    </span>
                </router-link>
                <div>
                    <form @submit="goToSearchPage">
                        <Input
                            type="search"
                            placeholder="Search..."
                            class="md:w-40 lg:w-64"
                            v-model="keyword"
                        />
                    </form>
                </div>
            </nav>
            <nav class="flex items-center space-x-2">
                <Button
                    @click="onOpenGithub0auth"
                    :loading="loading"
                    v-if="!authStore.isAuthenticated && showLoginButton"
                >
                    <template v-if="loading">
                        <CustomLoadingSpinner class="mr-2 w-4 h-4" />
                    </template>
                    <template> </template>
                    <GithubIcon class="mr-2" />
                    Login via GitHub</Button
                >
                <template v-if="authStore.isAuthenticated">
                    <Button variant="outline" as-child>
                        <router-link
                            :to="{
                                name: 'post-form-page',
                                params: { operation: 'new' },
                            }"
                        >
                            <PlusIcon class="mr-1" />Create Post
                        </router-link>
                    </Button>
                    <BaseUserMenuDropdown />
                </template>
            </nav>
        </div>
    </header>
</template>
