<script setup lang="ts">
import { cn } from "@/lib/utils.ts";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { computed, ref, watch } from "vue";
import MiniProfileCard from "@/components/MiniProfileCard.vue";
import MorePostsCard from "@/components/MorePostsCard.vue";
import { Separator } from "@/components/ui/separator";
import { Delta } from "@vueup/vue-quill";
import { usePostStore } from "@/stores/post.ts";
import { useToast } from "@/components/ui/toast";
import { useRoute, useRouter } from "vue-router";
import PostUserItem from "@/components/PostUserItem.vue";
import CustomTextEditor from "@/components/CustomTextEditor.vue";
import PostEngagementSidebar from "@/components/PostEngagementSidebar.vue";
import { useAuthStore } from "@/stores/auth.ts";
import PostCommentSection from "@/components/PostCommentSection.vue";
import SyntaxSquadAboutCard from "@/components/SyntaxSquadAboutCard.vue";

const postStore = usePostStore();
const { toast } = useToast();
const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const loading = ref(true);
const post = ref(null);
const content = ref(null);
const reactions = ref(0);
const comments = ref(0);
const bookmarks = ref(0);
const isEngagementSidebarComponentReady = ref(false);

const slug = computed(() => route.params.slug);
const isPageReady = computed(
    () =>
        post.value && isEngagementSidebarComponentReady.value && !loading.value,
);
const user = computed(() =>
    authStore.isAuthenticated ? authStore.user : null,
);
const isSelf = computed(() =>
    authStore.isAuthenticated ? post.value.user.id === user.value.id : false,
);

watch(
    () => route.params.slug,
    async () => {
        isEngagementSidebarComponentReady.value = false;
        await getPost();
        isEngagementSidebarComponentReady.value = true;
    },
);

const getPost = async () => {
    loading.value = true;
    const result = await postStore.getBySlug(slug.value);
    if (result.success) {
        await setPost(result.data);
        loading.value = false;
        return;
    }
    await router.go(-1);
    toast({
        title: "Something went wrong.",
        description: result.message,
    });
};
const setPost = async (_post) => {
    post.value = Object.assign({}, _post);

    reactions.value = post.value.reactions_count;
    comments.value = post.value.comments_count;
    bookmarks.value = post.value.bookmarks_count;

    content.value = new Delta(JSON.parse(post.value.content.value));
    await checkIfDraft(post.value.is_draft);
};
const checkIfDraft = async (isDraft) => {
    if (isDraft) {
        toast({
            title: "Something went wrong.",
            description: `You're not allowed to access this resource.`,
        });
        await router.push({ name: "home-page" });
    }
};

if (slug) {
    getPost();

    if (!authStore.isAuthenticated) {
        isEngagementSidebarComponentReady.value = true;
    }
}
</script>

<template>
    <div
        class="md:pt-10 md:container md:flex md:flex-col lg:flex-row lg:items-start lg:gap-5 h-screen"
    >
        <template v-if="!isPageReady">
            <div class="flex justify-center w-full">
                <img
                    class="w-auto h-20 md:h-40"
                    src="/nyan-cat.gif"
                    alt="Auth GIF"
                />
            </div>
        </template>
        <div
            class="md:w-10"
            :class="{ hidden: !isPageReady }"
            v-if="authStore.isAuthenticated"
        >
            <div class="fixed">
                <PostEngagementSidebar
                    :id="post.id"
                    v-model:reactions="reactions"
                    v-model:comments="comments"
                    v-model:bookmarks="bookmarks"
                    v-model:ready="isEngagementSidebarComponentReady"
                    v-if="post"
                />
            </div>
        </div>
        <template v-if="isPageReady">
            <div class="md:w-8/12">
                <div class="relative md:pb-10">
                    <Card
                        :class="
                            cn('rounded-none md:rounded-lg', $attrs.class ?? '')
                        "
                    >
                        <img
                            class="w-full h-80 object-cover"
                            :src="post.cover_image_url"
                            :alt="post.title"
                            v-if="post.cover_image_url"
                        />
                        <CardContent class="space-y-5 mt-6">
                            <PostUserItem
                                :user="post.user"
                                :created-at="post.created_at"
                                :reactions="reactions"
                                :comments="comments"
                                :bookmarks="bookmarks"
                            />
                            <div>
                                <div class="space-y-3">
                                    <p
                                        class="text-3xl md:text-4xl lg:text-5xl font-black leading-none tracking-tight break-words"
                                    >
                                        {{ post.title }}
                                    </p>
                                    <div class="flex gap-1 flex-wrap">
                                        <template
                                            v-for="(tag, index) in post.tags"
                                            :key="index"
                                        >
                                            <Badge variant="outline">
                                                <router-link
                                                    :to="{
                                                        name: 'search-page',
                                                        query: {
                                                            keyword: tag.name,
                                                        },
                                                    }"
                                                >
                                                    <span
                                                        class="font-light lowercase text-sm hover:cursor-pointer hover:underline"
                                                    >
                                                        {{ tag.name }}
                                                    </span>
                                                </router-link>
                                            </Badge>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                        <div class="pt-5 pb-8">
                            <CustomTextEditor mode="read" v-model="content" />
                        </div>
                        <Separator />
                        <CardContent class="pt-6 space-y-10">
                            <PostCommentSection :post-id="post.id" />
                        </CardContent>
                    </Card>
                </div>
            </div>
            <div class="hidden md:block md:w-2/6">
                <div class="space-y-5">
                    <MiniProfileCard :user="post.user" />
                    <MorePostsCard
                        :exclude="[post.id]"
                        :user-id="post.user.id"
                        v-if="!isSelf"
                    />
                    <SyntaxSquadAboutCard />
                </div>
            </div>
        </template>
    </div>
</template>
