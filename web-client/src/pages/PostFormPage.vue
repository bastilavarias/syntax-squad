<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import CustomTextEditor from "@/components/CustomTextEditor.vue";
import PostingGuideCard from "@/components/PostingGuideCard.vue";
import SelfPostListCard from "@/components/SelfPostListCard.vue";
import {
    TagsInput,
    TagsInputInput,
    TagsInputItem,
    TagsInputItemDelete,
    TagsInputItemText,
} from "@/components/ui/tags-input";
import { computed, ref, watch } from "vue";
import CardFooter from "@/components/ui/card/CardFooter.vue";
import { Button } from "@/components/ui/button";
import { CreatePostPayload, usePostStore } from "@/stores/post.ts";
import { useToast } from "@/components/ui/toast/use-toast";
import { useRoute, useRouter } from "vue-router";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { ServerOffIcon, TriangleAlertIcon, Table2Icon } from "lucide-vue-next";
import CustomLoadingSpinner from "@/components/CustomLoadingSpinner.vue";
import { useAuthStore } from "@/stores/auth.ts";
import { Delta } from "@vueup/vue-quill";
import { Badge } from "@/components/ui/badge";
import PageContainer from "@/components/PageContainer.vue";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";

const postStore = usePostStore();
const { toast } = useToast();
const router = useRouter();
const authStore = useAuthStore();
const route = useRoute();

const defaultForm = {
    title: null,
    content: null,
    tags: [],
    is_draft: 0,
    cover_image: null,
    cover_image_url: null,
};
const form = ref(Object.assign({}, defaultForm));
const error = ref(null);
const formLoading = ref(false);
const postDetailsLoading = ref(false);
const operation = ref(route.params.operation);
const postID = ref(route.query.postID ?? null);
const isUploadedCoverImageShow = ref(false);
const isDraftPost = ref(null);
const shouldShowDialog = ref(false);

const user = computed(() =>
    authStore.isAuthenticated ? authStore.user : null,
);
const formTitle = computed(() =>
    operation.value === "new" ? "Create new Post" : "Edit Post",
);
const showPublishButton = computed(
    () => operation.value === "new" || isDraftPost.value,
);
const showUpdateButton = computed(
    () => operation.value === "edit" && !isDraftPost.value,
);
const coverImageInputLabel = computed(() =>
    operation.value === "new" ? "Cover Image" : "New Cover Image",
);

watch(
    () => route.params.operation,
    () => {
        window.location.reload();
    },
);
watch(
    () => route.query.postID,
    () => {
        window.location.reload();
    },
);

const onFileInputChange = (event: Element) => {
    const file = event.target.files[0];
    if (file) {
        form.value.cover_image = file;
    }
};
const onCreate = async () => {
    formLoading.value = true;
    const payload: CreatePostPayload = {
        ...form.value,
        content: JSON.stringify(form.value.content),
        is_draft: 0,
    };
    const result = await postStore.create(payload);
    if (result.success) {
        toast({
            title: "Post successfully published",
            description:
                "Congratulations! Your post has been successfully published and is now viewable to the public. You will be redirected to your post shortly.",
        });
        await router.push({
            name: "view-post-page",
            params: { username: user.value.username, slug: result.data.slug },
        });

        return;
    }
    formLoading.value = false;
    error.value = result.message;
};
const getPost = async () => {
    postDetailsLoading.value = true;
    const result = await postStore.get(postID.value);
    if (result.success) {
        const post = result.data;
        await checkIfAuthor(post.user_id);
        form.value = Object.assign(
            {},
            {
                ...defaultForm,
                title: post.title,
                content: new Delta(JSON.parse(post.content.value)),
                cover_image_url: post.cover_image_url,
                tags: post.tags.map((tag) => tag.name),
            },
        );
        isDraftPost.value = post.is_draft || null;
        postDetailsLoading.value = false;
        return;
    }
    toast({
        title: "Something went wrong.",
        description: result.message,
    });
};
const checkIfAuthor = async (authorID) => {
    if (authorID !== user.value.id) {
        toast({
            title: "Something went wrong.",
            description: `You're not allowed to access this resource.`,
        });
        await router.push({ name: "home-page" });
    }
};
const onUpdate = async () => {
    formLoading.value = true;
    const payload = {
        ...form.value,
        content: JSON.stringify(form.value.content),
        _method: "PUT",
        post_id: postID.value,
        is_draft: 0,
    };
    const result = await postStore.update(payload);
    if (result.success) {
        toast({
            title: "Post successfully updated",
            description:
                "Congratulations! Your post has been successfully updated. You will be redirected to your post shortly.",
        });
        await router.push({
            name: "view-post-page",
            params: { username: user.value.username, slug: result.data.slug },
        });

        return;
    }
    formLoading.value = false;
    error.value = result.message;
};
const onSaveDraft = async () => {
    formLoading.value = true;
    const payload = {
        ...form.value,
        content: JSON.stringify(form.value.content),
        is_draft: 1,
    };
    let result;
    if (postID.value && operation.value === "edit") {
        payload.post_id = postID.value;
        payload._method = "PUT";
        result = await postStore.update(payload);
    } else {
        result = await postStore.create(payload);
    }
    if (result.success) {
        toast({
            title: "Post successfully drafted",
            description: "Your post has been successfully drafted.",
        });
        window.location.reload();
        return;
    }
    formLoading.value = false;
    error.value = result.message;
};
const onOpenDialog = () => {
    shouldShowDialog.value = !shouldShowDialog.value;
};

if (postID.value) {
    getPost();
}
</script>

<template>
    <PageContainer>
        <div class="grid grid-cols-12 md:gap-5">
            <div class="col-span-12 md:col-span-7">
                <template v-if="postDetailsLoading">
                    <div class="flex flex-col items-center">
                        <img
                            class="w-auto h-20 md:h-40"
                            src="/nyan-cat.gif"
                            alt="Auth GIF"
                        />
                    </div>
                </template>
                <template v-else>
                    <div class="relative space-y-3">
                        <Alert
                            class="rounded-none shadow-none border-0 md:rounded-lg md:shadow-sm md:border"
                        >
                            <ServerOffIcon class="w-4 h-4" />
                            <AlertTitle
                                >Hey! Just a friendly reminder... 👮
                            </AlertTitle>
                            <AlertDescription>
                                Due to limited server resources, you are only
                                permitted to publish one post per day. Please
                                refrain from spamming and ensure that each post
                                respects the content payload size limit of 3MB.
                            </AlertDescription>
                        </Alert>
                        <Card
                            class="bg-white rounded-none shadow-none border-0 md:rounded-lg md:shadow-sm md:border"
                        >
                            <CardHeader class="flex justify-between">
                                <CardTitle
                                    class="flex justify-between items-center"
                                >
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        {{ formTitle }}
                                        <Badge
                                            :variant="
                                                isDraftPost
                                                    ? 'outline'
                                                    : 'default'
                                            "
                                            class="tracking-wide ml-1"
                                            v-if="operation === 'edit'"
                                        >
                                            <template v-if="isDraftPost"
                                                >Unpublished
                                            </template>
                                            <template v-else
                                                >Published</template
                                            >
                                        </Badge>
                                    </div>

                                    <Button
                                        class="md:hidden"
                                        variant="ghost"
                                        size="icon"
                                        @click="onOpenDialog"
                                    >
                                        <Table2Icon class="w-4 h-4" />
                                    </Button>
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="grid items-center w-full gap-4">
                                    <template v-if="!!error">
                                        <Alert variant="destructive">
                                            <TriangleAlertIcon
                                                class="w-4 h-4"
                                            />
                                            <AlertTitle
                                                >Request Error</AlertTitle
                                            >
                                            <AlertDescription>
                                                {{ error }}
                                            </AlertDescription>
                                        </Alert>
                                    </template>
                                    <div class="flex flex-col space-y-2">
                                        <Label for="title">Title</Label>
                                        <Input
                                            id="title"
                                            placeholder="New post title..."
                                            v-model="form.title"
                                        />
                                    </div>
                                    <div class="flex flex-col space-y-1.5">
                                        <Label for="picture">{{
                                            coverImageInputLabel
                                        }}</Label>
                                        <Input
                                            id="picture"
                                            type="file"
                                            accept="image/png, image/jpg, image/jpeg"
                                            @change="onFileInputChange"
                                        />
                                        <template v-if="form.cover_image_url">
                                            <div class="flex justify-between">
                                                <div></div>
                                                <small
                                                    class="underline text-blue-600 cursor-pointer"
                                                    @click="
                                                        isUploadedCoverImageShow =
                                                            !isUploadedCoverImageShow
                                                    "
                                                    >{{
                                                        isUploadedCoverImageShow
                                                            ? "Hide"
                                                            : "Show"
                                                    }}
                                                    uploaded cover image</small
                                                >
                                            </div>
                                            <img
                                                class="w-full h-auto"
                                                :alt="form.title"
                                                :src="form.cover_image_url"
                                                v-if="isUploadedCoverImageShow"
                                            />
                                        </template>
                                    </div>
                                    <div class="flex flex-col space-y-2">
                                        <Label for="tags">Tags</Label>
                                        <TagsInput v-model="form.tags">
                                            <TagsInputItem
                                                v-for="item in form.tags"
                                                :key="item"
                                                :value="item"
                                            >
                                                <TagsInputItemText />
                                                <TagsInputItemDelete />
                                            </TagsInputItem>

                                            <TagsInputInput />
                                        </TagsInput>
                                    </div>
                                    <div>
                                        <Label for="tags">Content</Label>
                                        <div class="pt-2"></div>
                                        <CustomTextEditor
                                            v-model="form.content"
                                        />
                                    </div>
                                </div>
                            </CardContent>
                            <CardFooter>
                                <Button
                                    :disabled="formLoading"
                                    @click="onCreate"
                                    v-if="showPublishButton"
                                >
                                    <template v-if="formLoading">
                                        <CustomLoadingSpinner
                                            class="mr-2 w-4 h-4"
                                        />
                                    </template>
                                    Publish
                                </Button>
                                <Button
                                    :disabled="formLoading"
                                    @click="onUpdate"
                                    v-if="showUpdateButton"
                                >
                                    <template v-if="formLoading">
                                        <CustomLoadingSpinner
                                            class="mr-2 w-4 h-4"
                                        />
                                    </template>
                                    Update post
                                </Button>
                                <Button
                                    variant="ghost"
                                    :disabled="formLoading"
                                    @click="onSaveDraft"
                                >
                                    <template v-if="formLoading">
                                        <CustomLoadingSpinner
                                            class="mr-2 w-4 h-4"
                                        />
                                    </template>
                                    Save as Draft
                                </Button>
                            </CardFooter>
                        </Card>
                    </div>
                </template>
            </div>
            <div
                class="max-md:hidden flex col-span-3 md:col-span-5 flex-col gap-y-4"
            >
                <SelfPostListCard />
                <PostingGuideCard />
            </div>
        </div>
        <Dialog v-model:open="shouldShowDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Your Posts</DialogTitle>
                </DialogHeader>

                <SelfPostListCard :simple="true" />
            </DialogContent>
        </Dialog>
    </PageContainer>
</template>
