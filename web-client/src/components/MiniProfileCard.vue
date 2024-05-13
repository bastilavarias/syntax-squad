<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { computed, onMounted, ref } from "vue";
import { useAuthStore } from "@/stores/auth.ts";
import { useUserFollowerStore } from "@/stores/userFollower.ts";
import CustomLoadingSpinner from "@/components/CustomLoadingSpinner.vue";
import { useCustomComposable } from "@/custom-composable.ts";

const props = defineProps(["user"]);

const authStore = useAuthStore();
const userFollowerStore = useUserFollowerStore();
const customComposable = useCustomComposable();

const alreadyFollowed = ref(false);
const loading = ref(true);
const followersCountLocal = ref(props.user.followers_count || 0);

const authuser = computed(() =>
    authStore.isAuthenticated ? authStore.user : null
);
const isSelf = computed(() =>
    authStore.isAuthenticated ? props.user.id === authuser.value.id : false
);

const onClick = async () => {
    loading.value = true;
    const payload = {
        user_id: props.user.id,
    };
    let result;
    if (alreadyFollowed.value) {
        alreadyFollowed.value = false;
        followersCountLocal.value -= 1;
        result = await userFollowerStore.delete(payload);
    } else {
        alreadyFollowed.value = true;
        followersCountLocal.value += 1;
        result = await userFollowerStore.create(payload);
    }
    loading.value = false;
    if (result.success) {
        return;
    }
};
const onCheck = async () => {
    const payload = {
        user_id: props.user.id,
    };
    const result = await userFollowerStore.check(payload);
    if (result.success) {
        alreadyFollowed.value = result.data || false;
        loading.value = false;
        return;
    }
};

onMounted(async () => {
    try {
        if (authStore.isAuthenticated) {
            await onCheck();
        }
    } catch (_) {}
});
</script>

<template>
    <Card>
        <CardHeader class="py-4">
            <div class="flex items-center space-x-4">
                <Avatar>
                    <AvatarImage :src="user.avatar_url" />
                    <AvatarFallback>{{ user.name[0] }}</AvatarFallback>
                </Avatar>
                <div class="space-y-1">
                    <router-link
                        :to="{
                            name: 'profile-page',
                            params: { username: user.username },
                        }"
                    >
                        <p
                            class="font-medium leading-none text-black hover:underline hover:cursor-pointer"
                        >
                            {{ customComposable.limitString(user.name, 70) }}
                        </p>
                    </router-link>
                    <router-link
                        :to="{
                            name: 'profile-page',
                            params: { username: user.username },
                        }"
                    >
                        <p
                            class="text-xs text-muted-foreground hover:underline hover:cursor-pointer"
                        >
                            {{
                                customComposable.limitString(user.username, 70)
                            }}
                        </p>
                    </router-link>
                </div>
            </div>
        </CardHeader>
        <CardContent class="space-y-5">
            <div class="flex space-x-4">
                <p class="text-xs text-muted-foreground">
                    {{ user.posts_count }} Posts
                </p>
                <p class="text-xs text-muted-foreground">
                    {{ followersCountLocal }} Followers
                </p>
                <p class="text-xs text-muted-foreground">
                    {{ user.followings_count }} Following
                </p>
            </div>
            <p class="text-sm" v-if="user.bio">
                {{ user.bio }}
            </p>
            <Button
                :variant="alreadyFollowed ? 'outline' : 'default'"
                class="w-full"
                :disabled="loading"
                @click="onClick"
                v-if="authStore.isAuthenticated && !isSelf"
            >
                <template v-if="loading">
                    <CustomLoadingSpinner class="mr-2 w-4 h-4" /> </template
                ><template v-else>{{
                    alreadyFollowed ? "Unfollow" : "Follow"
                }}</template></Button
            >
            <Button
                variant="outline"
                class="w-full"
                :disabled="true"
                v-if="isSelf"
            >
                You
            </Button>
        </CardContent>
        <Separator></Separator>
    </Card>
</template>
