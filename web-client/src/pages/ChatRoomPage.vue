<script setup lang="ts">
import { ScrollArea } from "@/components/ui/scroll-area";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { ref, computed } from "vue";
import { ListChatsPayload, useChatStore } from "@/stores/chat.ts";
import InfiniteLoading from "v3-infinite-loading";
import { useToast } from "@/components/ui/toast";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth.ts";
import ChatRoomButtonItem from "@/components/ChatRoomButtonItem.vue";

const chatStore = useChatStore();
const { toast } = useToast();
const router = useRouter();

const loading = ref(true);
const roomsIdentifier = ref(0);

const roomContents = computed(() => chatStore.room.value);

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
</script>

<template>
    <Card class="h-full">
        <CardHeader class="h-[8%]">
            <CardTitle>Chats</CardTitle>
        </CardHeader>
        <CardContent class="h-[92%]">
            <ScrollArea class="h-full">
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
                                class="w-auto h-40"
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
