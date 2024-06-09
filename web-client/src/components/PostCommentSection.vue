<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Textarea } from "@/components/ui/textarea";
import { ref, computed } from "vue";
import {
    CreatePostCommentPayload,
    ListPostCommentsPayload,
    usePostCommentStore,
} from "@/stores/postComment.ts";
import { useToast } from "@/components/ui/toast";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth.ts";
import CustomLoadingSpinner from "@/components/CustomLoadingSpinner.vue";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { TriangleAlertIcon } from "lucide-vue-next";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";
import { formatDistanceToNow } from "date-fns";

const props = defineProps(["postId"]);

const postCommentStore = usePostCommentStore();
const { toast } = useToast();
const router = useRouter();
const authStore = useAuthStore();

const page = ref(1);
const perPage = ref(5);
const formLoading = ref(false);
const comments = ref([]);
const commentsIdentifier = ref(0);
const comment = ref("");
const error = ref(null);
const commentsCount = ref(0);

const isAuthenticated = computed(() => authStore.isAuthenticated);
const user = computed(() => (isAuthenticated.value ? authStore.user : null));

const getComments = async ($state) => {
    const payload: ListPostCommentsPayload = {
        post_id: props.postId,
        page: page.value,
        per_page: perPage.value,
    };
    const result = await postCommentStore.list(payload);
    if (result.success) {
        commentsCount.value = result.data.total || 0;
        const _comments = result.data.data;
        comments.value = [...comments.value, ..._comments];
        if (_comments.length >= perPage.value) {
            $state.loaded();
        } else {
            $state.complete();
        }
        page.value += 1;
        return;
    }
    await router.push({ name: "view-post-page" });
    toast({
        variant: "destructive",
        title: "Server error.",
        description: result.message,
    });
};
const onCreateComment = async () => {
    const payload: CreatePostCommentPayload = {
        post_id: props.postId,
        content: comment.value,
    };
    formLoading.value = true;
    const result = await postCommentStore.create(payload);
    if (result.success) {
        comment.value = "";
        comments.value = [result.data, ...comments.value];
        formLoading.value = false;
        toast({
            title: "Comment successfully submitted!",
            description: "Your comment was successfully added to discussion.",
        });
        return;
    }
    formLoading.value = false;
    error.value = result.message;
};
</script>

<template>
    <div class="space-y-5">
        <p class="text-2xl font-bold">
            Comments {{ commentsCount > 0 ? `(${commentsCount})` : "" }}
        </p>
        <template v-if="!!error">
            <Alert variant="destructive">
                <TriangleAlertIcon class="w-4 h-4" />
                <AlertTitle>Request Error</AlertTitle>
                <AlertDescription>
                    {{ error }}
                </AlertDescription>
            </Alert>
        </template>
        <div class="flex space-x-2 items-start" v-if="isAuthenticated">
            <Avatar class="w-6 h-6">
                <AvatarImage :src="user.avatar_url" />
                <AvatarFallback>{{ user.name[0] }}</AvatarFallback>
            </Avatar>
            <div class="w-full space-y-2">
                <div>
                    <Textarea
                        class="w-full"
                        placeholder="Add to the discussion"
                        v-model="comment"
                    />
                    <small class="text-xs text-muted-foreground"
                        >{{ comment.length }}/200</small
                    >
                </div>
                <div class="flex justify-between">
                    <div></div>
                    <Button :disabled="formLoading" @click="onCreateComment">
                        <template v-if="formLoading">
                            <CustomLoadingSpinner class="mr-2 w-4 h-4" />
                        </template>
                        Submit
                    </Button>
                </div>
            </div>
        </div>
    </div>
    <div class="space-y-4">
        <template v-for="(comment, index) in comments" :key="index">
            <div class="flex space-x-1 items-start">
                <Avatar class="w-6 h-6">
                    <AvatarImage :src="comment.user.avatar_url" />
                    <AvatarFallback>{{ comment.user.name[0] }}</AvatarFallback>
                </Avatar>
                <div
                    class="bg-gray-50 rounded p-2 text-sm font-light space-y-2 w-full"
                >
                    <div>
                        {{ comment.user.name }}
                        <small
                            >{{
                                formatDistanceToNow(comment.created_at)
                            }}
                            ago</small
                        >
                    </div>
                    <p>
                        {{ comment.content }}
                    </p>
                </div>
            </div>
        </template>
        <InfiniteLoading
            class="pt-5"
            :identifier="commentsIdentifier"
            @infinite="getComments"
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
                        >End of post comments.</span
                    >
                </div>
            </template>
        </InfiniteLoading>
    </div>
</template>
