<script setup lang="ts">
import { ScrollArea } from "@/components/ui/scroll-area";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { ref, computed, onBeforeMount, watch, onUnmounted } from "vue";
import { ListChatsPayload, useChatStore } from "@/stores/chat.ts";
import InfiniteLoading from "v3-infinite-loading";
import { useToast } from "@/components/ui/toast";
import { useRouter } from "vue-router";
import ChatRoomButtonItem from "@/components/ChatRoomButtonItem.vue";
import CustomSocketConnectionBadge from "@/components/CustomSocketConnectionBadge.vue";
import { socket, state as socketState } from "@/socket";
import { useAuthStore } from "@/stores/auth.ts";

const chatStore = useChatStore();
const { toast } = useToast();
const router = useRouter();
const authStore = useAuthStore();

const loading = ref(true);
const roomsIdentifier = ref(0);

const authuser = computed(() =>
    authStore.isAuthenticated ? authStore.user : null,
);

const roomContents = computed(() => chatStore.room.value);

watch(
    () => socketState.connected,
    () => {
        if (socketState.connected) {
            establishSocketListener();
        }
    },
);

const getRooms = async ($state) => {
    const payload: ListChatsPayload = {
        page: chatStore.room.page,
        per_page: chatStore.room.perPage,
    };
    loading.value = true;
    const result = await chatStore.list(payload);
    if (result.success) {
        loading.value = false;
        const _chats = result.data.data;
        chatStore.room.value = [...chatStore.room.value, ..._chats];
        if (_chats.length >= chatStore.room.perPage) {
            $state.loaded();
        } else {
            $state.complete();
        }
        chatStore.room.page += 1;
        return;
    }
    await router.push({ name: "home-page" });
    toast({
        variant: "destructive",
        title: "Server error.",
        description: result.message,
    });
};
const establishSocketListener = () => {
    socket.on(`user-${authuser.value.id}`, (data) => {
        if (
            [...chatStore.room.value].map((room) => room.id).includes(data.id)
        ) {
            chatStore.room.value = chatStore.room.value.filter(
                (room) => room.id !== data.id,
            );
        }
        chatStore.room.value = [data, ...chatStore.room.value];
    });
};

onBeforeMount(async () => {
    if (!authuser.value.id) {
        await router.push({ name: "home-page" });
        toast({
            variant: "destructive",
            title: "Invalid room!",
            description: "You are entering a room that you are not a part of.",
        });
    }
    socket.connect();
});

onUnmounted(() => {
    socket.disconnect();
});
</script>

<template>
    <Card class="h-full">
        <CardHeader class="h-[8%]">
            <CardTitle class="flex justify-between"
                ><span>Chats</span>
                <CustomSocketConnectionBadge />
            </CardTitle>
        </CardHeader>
        <CardContent class="h-[92%]">
            <ScrollArea class="h-full pt-3">
                <div class="space-y-3">
                    <template
                        v-for="(room, index) in roomContents"
                        :key="index"
                    >
                        <ChatRoomButtonItem :room="room" />
                    </template>
                </div>
                <InfiniteLoading
                    class="pt-5"
                    :identifier="roomsIdentifier"
                    @infinite="getRooms"
                >
                    <template #spinner>
                        <div class="flex flex-col items-center">
                            <img
                                class="w-auto h-20 md:h-40"
                                src="/nyan-cat.gif"
                                alt="Auth GIF"
                            />
                        </div>
                    </template>
                    <template #complete>
                        <div class="flex flex-col items-center">
                            <span class="text-muted-foreground text-xs"
                                >End of your rooms.</span
                            >
                        </div>
                    </template>
                </InfiniteLoading>
            </ScrollArea>
        </CardContent>
    </Card>
</template>
