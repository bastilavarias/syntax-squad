<script setup lang="ts">
import { useAuthStore } from "@/stores/auth.ts";

const authStore = useAuthStore();
</script>

<template>
    <aside class="overflow-y-auto pt-2">
        <div
            data-radix-scroll-area-viewport=""
            style="overflow: hidden scroll"
            class="h-full w-full rounded-[inherit]"
            tabindex="0"
        >
            <div style="min-width: 100%; display: table">
                <!--[--><!--[-->
                <div class="w-full">
                    <!--[-->
                    <div class="pb-4">
                        <div class="grid grid-flow-row auto-rows-max text-sm">
                            <router-link :to="{ name: 'home-page' }"
                                ><span
                                    class="group flex w-full items-center rounded-md border border-transparent"
                                >
                                    <span class="text-2xl mr-3">🏠</span>
                                    <span class="text-lg font-medium"
                                        >Home</span
                                    >
                                </span>
                            </router-link>
                        </div>
                    </div>
                    <div class="pb-4" v-if="authStore.isAuthenticated">
                        <div class="grid grid-flow-row auto-rows-max text-sm">
                            <router-link :to="{ name: 'chat-page' }"
                                ><span
                                    class="group flex w-full items-center rounded-md border border-transparent"
                                >
                                    <span class="text-2xl mr-3">💬</span>
                                    <span class="text-lg font-medium"
                                        >Chats</span
                                    >
                                </span>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</template>
