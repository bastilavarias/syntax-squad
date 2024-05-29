<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { useRoute, useRouter } from "vue-router";
import { GithubIcon, PlusIcon, Search } from "lucide-vue-next";
import { Input } from "@/components/ui/input";
import BaseUserMenuDropdown from "@/components/BaseUserMenuDropdown.vue";
import CustomLoadingSpinner from "@/components/CustomLoadingSpinner.vue";
import { computed, ref, watch } from "vue";
import { useAuthStore } from "@/stores/auth.ts";
import BaseSidebar from "@/components/BaseSidebar.vue";
import AppLogo from "@/AppLogo.vue";
import SearchBar from "@/SearchBar.vue";

const mobileSearch = ref(null);
const router = useRouter();
const authStore = useAuthStore();
const route = useRoute();

const loading = ref(false);
const keyword = ref(null);
const showMobileSearch = ref(false);

const showLoginButton = computed(() => route.name !== "auth-callback-page");

watch(
    () => route.query.keyword,
    (value) => {
        if (value) {
            keyword.value = value;
        }
    }
);

const toggleMobileSearch = () => {
    showMobileSearch.value = !showMobileSearch.value;
};
const goToSearchPage = (e) => {
    e.preventDefault();
    if (keyword.value) {
        router.push({ name: "search-page", query: { keyword: keyword.value } });
        showMobileSearch.value = false;
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
        class="fixed w-screen min-h-14 flex flex-col z-20 top-0 bg-background/80 backdrop-blur-lg border-b border-border"
    >
        <div class="px-4 lg:container flex items-center h-14 gap-x-2">
            <BaseSidebar />
            <nav class="flex items-center gap-x-2">
                <AppLogo class="" />

                <Search @click="toggleMobileSearch" class="lg:hidden" />
                <div class="max-lg:hidden md:w-40 lg:w-64">
                    <SearchBar ref="mobileSearch" @go-search="goToSearchPage" v-model="keyword"></SearchBar>
                </div>
            </nav>
            <nav class="flex items-center gap-x-2 ml-auto">
                <Button
                    @click="onOpenGithub0auth"
                    :loading="loading"
                    v-if="!authStore.isAuthenticated && showLoginButton"
                >
                    <template v-if="loading">
                        <CustomLoadingSpinner class="mr-2 w-4 h-4" />
                    </template>
                    <template></template>
                    <GithubIcon class="mr-2" />
                    Login
                    <span class="max-lg:hidden">&nbsp;via GitHub</span>
                </Button
                >
                <template v-if="authStore.isAuthenticated">
                    <Button variant="outline" as-child>
                        <router-link
                            :to="{
                                name: 'post-form-page',
                                params: { operation: 'new' },
                            }"
                        >
                            <PlusIcon />
                            <span class="max-sm:hidden ml-1">Create&nbsp;Post</span>
                        </router-link>
                    </Button>
                    <BaseUserMenuDropdown />
                </template>
            </nav>
        </div>
        <div v-if="showMobileSearch" class="px-4 pb-2 w-full lg:hidden">
            <SearchBar ref="mobileSearch" @go-search="goToSearchPage" v-model="keyword"></SearchBar>
        </div>
    </header>
</template>
