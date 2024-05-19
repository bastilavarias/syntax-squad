<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { useAuthStore } from "@/stores/auth.ts";
import { computed } from "vue";
import { formatDistanceToNow, toDate, parseISO } from "date-fns";
import { ReadChatPayload, useChatStore } from "@/stores/chat.ts";

const props = defineProps(["message", "otherMember"]);

const authStore = useAuthStore();
const chatStore = useChatStore();

const authuser = computed(() =>
    authStore.isAuthenticated ? authStore.user : null
);
const isYou = computed(() => props.message.user_id === authuser.value.id);
const senderFirstName = computed(() =>
    props.message.user.name
        ? props.message.user.name.split(" ")[0]
        : props.message.user.username
);
const userReceiver = computed(() =>
    props.message.user_id === props.otherMember.user.id
        ? authuser.value
        : props.otherMember.user
);
const read = async () => {
    if (
        props.message.read_by_receiver === 0 &&
        authuser.value.id === userReceiver.value.id
    ) {
        const payload: ReadChatPayload = {
            message_id: props.message.id,
            is_read: 1,
            _method: "PUT",
        };
        await chatStore.read(payload);
        chatStore.room.value = chatStore.room.value.map((room) => {
            if (room.latest_message.id === props.message.id) {
                room.latest_message.read_by_receiver = 1;
            }

            return room;
        });
    }
};

read();
</script>

<template>
    <div>
        <template v-if="isYou">
            <div class="flex justify-between">
                <div></div>
                <div class="lg:max-w-[60%] spacy-2">
                    <div class="flex space-x-1 items-start">
                        <div
                            class="bg-gray-50 rounded p-2 text-sm font-light space-y-2"
                        >
                            <p>
                                {{ message.message }}
                            </p>
                            <div class="flex justify-between">
                                <div></div>
                                <small class="text-xs"
                                    >You sent
                                    {{
                                        formatDistanceToNow(message.created_at)
                                    }}
                                    ago</small
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-else>
            <div class="lg:max-w-[60%]">
                <div class="flex space-x-1 items-start">
                    <Avatar class="w-6 h-6">
                        <AvatarImage :src="message.user.avatar_url" />
                        <AvatarFallback>{{ senderFirstName }}</AvatarFallback>
                    </Avatar>
                    <div
                        class="bg-gray-50 rounded p-2 text-sm font-light space-y-2"
                    >
                        <small class="text-xs"
                            ><span class="capitalize">{{
                                senderFirstName
                            }}</span>
                            sent
                            {{ formatDistanceToNow(message.created_at) }}
                            ago</small
                        >
                        <p>
                            {{ message.message }}
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
