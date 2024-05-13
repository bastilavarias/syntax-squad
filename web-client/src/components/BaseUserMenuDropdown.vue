<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth.ts";
import { computed } from "vue";

const router = useRouter();
const authStore = useAuthStore();
const route = useRoute();

const user = computed(() => authStore.user);

const goToProfilePage = () => {
    router.push({
        name: "profile-page",
        params: { username: user.value.username },
    });
};
const onLogout = () => {
    authStore.disableAuth();
    authStore.removeAuth();
    if (route.meta.requiresAuth) {
        router.push({ name: "home-page" });
    }
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="relative h-8 w-8 rounded-full">
                <Avatar class="h-8 w-8">
                    <AvatarImage :src="user.avatar_url" />

                    <AvatarFallback>SL</AvatarFallback>
                </Avatar>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-56" align="end">
            <DropdownMenuLabel class="font-normal flex">
                <div class="flex flex-col space-y-1">
                    <p class="text-sm font-medium leading-none">
                        {{ user.name }}
                    </p>
                    <p class="text-sm font-medium leading-none">
                        @{{ user.username }}
                    </p>
                    <p
                        class="text-xs leading-none text-muted-foreground"
                        v-if="user.email"
                    >
                        {{ user.email }}
                    </p>
                </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuGroup>
                <DropdownMenuItem @click="goToProfilePage">
                    Profile
                </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuItem @click="onLogout"> Logout </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
