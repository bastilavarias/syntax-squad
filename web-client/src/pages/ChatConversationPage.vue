<script setup lang="ts">
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Card, CardContent, CardFooter } from "@/components/ui/card";
import { SendIcon } from "lucide-vue-next";
import {
    CreateChatPayload,
    ListChatMessagesPayload,
    useChatStore,
} from "@/stores/chat.ts";
import { useToast } from "@/components/ui/toast";
import { useRoute, useRouter } from "vue-router";
import { computed, nextTick, onBeforeMount, ref, watch } from "vue";
import ChatConversationMessageItem from "@/components/ChatConversationMessageItem.vue";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";
import CustomLoadingSpinner from "@/components/CustomLoadingSpinner.vue";
import { useAuthStore } from "@/stores/auth.ts";
import CustomSocketConnectionBadge from "@/components/CustomSocketConnectionBadge.vue";
import { socket, state as socketState } from "@/socket";

const chatStore = useChatStore();
const { toast } = useToast();
const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const messages = ref([]);
const page = ref(1);
const perPage = ref(5);
const loading = ref(true);
const messagesIdentifier = ref(0);
const scrollElement = ref(null);
const room = ref(null);
const message = ref(null);
const formLoading = ref(false);
const inputElement = ref(null);
const roomInfoLoading = ref(false);

const authuser = computed(() =>
    authStore.isAuthenticated ? authStore.user : null
);
const roomID = computed(() => route.params.roomID);
const otherMember = computed(
    () =>
        room.value &&
        room.value.members.filter(
            (member) => member.user_id !== authuser.value.id
        )[0]
);

watch(
    () => socketState.connected,
    () => {
        if (socketState.connected) {
            establishSocketListener();
        }
    }
);

const getMessages = async ($state) => {
    const payload: ListChatMessagesPayload = {
        page: page.value,
        per_page: perPage.value,
        room_id: roomID.value,
    };
    loading.value = true;
    const result = await chatStore.get(payload);
    if (result.success) {
        loading.value = false;
        const _messages = result.data.data;
        messages.value = [..._messages, ...messages.value];
        await nextTick(() => {
            scrollToBottom();
        });
        if (_messages.length >= perPage.value) {
            $state.loaded();
        } else {
            $state.complete();
        }
        page.value += 1;
        return;
    }
};
const scrollToBottom = () => {
    scrollElement.value.scrollTop =
        scrollElement.value.clientHeight || scrollElement.value.scrollHeight;
};
const onSendMessage = async () => {
    formLoading.value = true;
    const payload: CreateChatPayload = {
        message: message.value,
        room_id: roomID.value,
    };
    const result = await chatStore.create(payload);
    if (result.success) {
        await nextTick(() => {
            formLoading.value = false;
            message.value = null;
            inputElement.value.focus = true;
        });
        return;
    }
    formLoading.value = false;
    toast({
        variant: "destructive",
        title: "Something went wrong!",
        description: result.message,
    });
};
const getRoom = async () => {
    roomInfoLoading.value = true;
    const result = await chatStore.getRoom({ room_id: roomID.value });
    if (result.success) {
        room.value = result.data.data;
        roomInfoLoading.value = false;
        return;
    }
    await router.push({ name: "chat-page" });
    toast({
        variant: "destructive",
        title: "Something went wrong!",
        description: result.message,
    });
};
const checkUserIfRoomMember = async () => {
    if (
        !room.value.members
            .map((member) => member.user_id)
            .includes(authuser.value.id)
    ) {
        await router.push({ name: "chat-page" });
        toast({
            variant: "destructive",
            title: "Invalid room!",
            description: "You are entering a room that you are not a part of.",
        });
    }
};
const establishSocketListener = () => {
    console.log("establish");
    console.log(socketState);
    socket.on(`room-${roomID.value}`, (data) => {
        messages.value = [...messages.value, data];
        nextTick(() => {
            scrollToBottom();
        });
    });
};

onBeforeMount(async () => {
    if (!roomID.value) {
        await router.push({ name: "chat-page" });
        toast({
            variant: "destructive",
            title: "Invalid room!",
            description: "You are entering a room that you are not a part of.",
        });
    }

    await getRoom();
    await checkUserIfRoomMember();
    socket.connect();
});
</script>

<template>
    <Card class="h-full relative">
        <div class="h-[10%] px-6 flex items-center justify-between">
            <div class="flex items-center justify-between">
                <template v-if="roomInfoLoading && !room">
                    <CustomLoadingSpinner class="w-4 h-4" />
                </template>
                <template v-else>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center space-x-4">
                            <Avatar>
                                <AvatarImage
                                    :src="otherMember.user.avatar_url"
                                />
                                <AvatarFallback>{{
                                    otherMember.user.name[0]
                                }}</AvatarFallback>
                            </Avatar>
                            <div class="flex justify-center flex-col">
                                <div>
                                    <div class="flex space-between">
                                        <div class="font-semibold">
                                            {{
                                                otherMember.user.username ||
                                                otherMember.user.name
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <CustomSocketConnectionBadge />
        </div>
        <div ref="scrollElement" class="h-[80%] overflow-auto">
            <CardContent class="h-auto space-y-3 pb-8">
                <InfiniteLoading
                    :identifier="messagesIdentifier"
                    @infinite="getMessages"
                    :top="true"
                >
                    <template #spinner>
                        <div class="flex flex-col items-center">
                            <CustomLoadingSpinner class="w-4 h-4" />
                        </div>
                    </template>
                    <template #complete>
                        <div></div>
                    </template>
                </InfiniteLoading>
                <div class="space-y-3">
                    <template v-for="(message, index) in messages" :key="index">
                        <ChatConversationMessageItem
                            :message="message"
                            :other-member="otherMember"
                        />
                    </template>
                </div>
            </CardContent>
        </div>
        <CardFooter class="h-[10%] absolute bottom-0 left-0 w-full space-x-1">
            <Input
                ref="inputElement"
                placeholder="Type a message here..."
                :disabled="formLoading"
                @keyup.enter="onSendMessage"
                autofocus
                v-model="message"
            />
            <Button
                size="icon"
                variant="ghost"
                :disabled="!message || formLoading"
                @click="onSendMessage"
                ><template v-if="formLoading">
                    <CustomLoadingSpinner class="w-4 h-4" />
                </template>
                <template v-else>
                    <SendIcon />
                </template>
            </Button>
        </CardFooter>
    </Card>
</template>
