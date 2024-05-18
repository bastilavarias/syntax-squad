<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { computed, ref } from "vue";
import { useAuthStore } from "@/stores/auth.ts";
import { formatDistanceToNow } from "date-fns";

const props = defineProps(["room"]);

const authStore = useAuthStore();

const authuser = computed(() =>
    authStore.isAuthenticated ? authStore.user : null
);
const otherMember = computed(
    () =>
        props.room.members.filter(
            (member) => member.user_id !== authuser.value.id
        )[0]
);
const lastMessageIsYou = computed(
    () => props.room.latest_message.user_id === authuser.value.id
);
const shouldShowUnreadBadge = computed(() =>
    !lastMessageIsYou.value
        ? props.room.latest_message.read_by_receiver === 0
        : false
);
</script>

<template>
    <router-link
        :to="{
            name: 'chat-conversation-page',
            params: { roomID: room.id },
        }"
    >
        <button
            class="w-full flex flex-col items-start gap-2 rounded-lg border p-3 text-left text-sm transition-all hover:bg-accent mb-3"
        >
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-2 w-full">
                    <div class="flex items-center space-x-4 w-full">
                        <Avatar>
                            <AvatarImage :src="otherMember.user.avatar_url" />
                            <AvatarFallback>{{
                                otherMember.user.name[0]
                            }}</AvatarFallback>
                        </Avatar>
                        <div class="flex-1">
                            <div class="w-full">
                                <div class="flex justify-between w-full">
                                    <div class="flex items-center space-x-1">
                                        <span
                                            class="flex h-2 w-2 rounded-full bg-blue-600"
                                            v-if="shouldShowUnreadBadge"
                                        />
                                        <span class="font-semibold">
                                            {{ otherMember.user.name }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-muted-foreground">
                                        {{
                                            formatDistanceToNow(
                                                room.latest_message.created_at
                                            )
                                        }}
                                    </span>
                                </div>
                            </div>

                            <div
                                class="line-clamp-2 text-xs text-muted-foreground"
                            >
                                {{
                                    lastMessageIsYou
                                        ? "You"
                                        : room.latest_message.user.name
                                }}: {{ room.latest_message.message }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </button>
    </router-link>
</template>
