<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Textarea } from "@/components/ui/textarea";
import CustomLoadingSpinner from "@/components/CustomLoadingSpinner.vue";
import { computed, ref } from "vue";
import { CreateChatRoomPayload, useChatStore } from "@/stores/chat.ts";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { TriangleAlertIcon } from "lucide-vue-next";
import { useRouter } from "vue-router";

const model = defineModel();
const props = defineProps(["user"]);

const chatStore = useChatStore();
const router = useRouter();

const message = ref("");
const loading = ref(false);
const error = ref(null);

const isFormValid = computed(() => {
    return (
        props.user && message.value.length >= 1 && message.value.length <= 200
    );
});

const onSendMessage = async () => {
    const payload: CreateChatRoomPayload = {
        message: message.value,
        member_id: props.user.id,
    };
    loading.value = true;
    const result = await chatStore.createRoom(payload);
    if (result.success) {
        await router.push({
            name: "chat-conversation-page",
            params: { roomID: result.data.id },
        });
        return;
    }
    loading.value = false;
    error.value = result.message;
};
</script>

<template>
    <Dialog v-model:open="model">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>New message</DialogTitle>
                <DialogDescription>
                    Start your conversation with {{ user.name }}
                </DialogDescription>
            </DialogHeader>

            <div class="w-full space-y-2">
                <template v-if="!!error">
                    <Alert variant="destructive">
                        <TriangleAlertIcon class="w-4 h-4" />
                        <AlertTitle>Request Error</AlertTitle>
                        <AlertDescription>
                            {{ error }}
                        </AlertDescription>
                    </Alert>
                </template>
                <div>
                    <Textarea
                        class="w-full"
                        placeholder="Type your message"
                        v-model="message"
                    />
                    <small class="text-xs text-muted-foreground"
                        >{{ message.length }}/200</small
                    >
                </div>
            </div>

            <DialogFooter>
                <Button
                    :disabled="loading || !isFormValid"
                    @click="onSendMessage"
                >
                    <template v-if="loading">
                        <CustomLoadingSpinner class="mr-2 w-4 h-4" />
                    </template>
                    Send
                </Button></DialogFooter
            >
        </DialogContent>
    </Dialog>
</template>
