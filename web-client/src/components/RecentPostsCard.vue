<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { ref } from "vue";
import { ListPostsPayload, usePostStore } from "@/stores/post.ts";
import { useToast } from "@/components/ui/toast";
import { useCustomComposable } from "@/custom-composable.ts";
import { formatDistanceToNow, formatDistanceToNowStrict } from "date-fns";
import { BookmarkIcon, HeartIcon, MessageCircleIcon } from "lucide-vue-next";

const { toast } = useToast();
const postStore = usePostStore();
const customComposable = useCustomComposable();

const loading = ref(true);
const posts = ref([]);

const getPosts = async () => {
    const payload: ListPostsPayload = {
        page: 1,
        per_page: 5,
        sort_by: "latest",
        is_draft: 0,
    };
    const result = await postStore.list(payload);
    if (result.success) {
        posts.value = result.data.data;
        loading.value = false;
        return;
    }
    toast({
        variant: "destructive",
        title: "Server error.",
        description: result.message,
    });
};

getPosts();
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="text-xl">Recent posts</CardTitle>
        </CardHeader>
        <CardContent>
            <div class="space-y-3">
                <template v-if="loading">
                    <div class="h-[100px] flex justify-center items-center">
                        <span class="text-xs text-muted-foreground">
                            Getting more posts...
                        </span>
                    </div>
                </template>
                <template v-if="!loading && posts.length === 0">
                    <div class="h-[100px] flex justify-center items-center">
                        <span class="text-xs text-muted-foreground">
                            No posts.
                        </span>
                    </div>
                </template>
                <template v-for="(post, index) in posts" :key="index">
                    <div class="flex gap-x-2">
                        <div class="flex flex-col gap-y-2">
                            <div class="flex flex-col space-y-1">
                                <router-link
                                    :to="{
                                        name: 'view-post-page',
                                        params: {
                                            username: post.user.username,
                                            slug: post.slug,
                                        },
                                    }"
                                >
                                    <p
                                        class="max-lg:text-sm mr-1 font-medium leading-none hover:underline hover:cursor-pointer"
                                        :title="`Posted ${formatDistanceToNow(post.created_at)} ago`"
                                    >
                                        {{
                                            customComposable.limitString(
                                                post.title,
                                                70,
                                            )
                                        }}
                                    </p>
                                </router-link>
                                <router-link
                                    :to="{
                                        name: 'profile-page',
                                        params: {
                                            username: post.user.username,
                                        },
                                    }"
                                >
                                    <p
                                        class="text-xs text-muted-foreground mr-1 leading-none hover:underline hover:cursor-pointer"
                                    >
                                        by
                                        {{
                                            customComposable.limitString(
                                                post.user.name ||
                                                    post.user.username,
                                                70,
                                            )
                                        }}
                                    </p>
                                </router-link>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex justify-start space-x-3">
                                    <span
                                        class="flex items-center text-sm text-muted-foreground gap-1"
                                    >
                                        <HeartIcon class="w-3 h-3" />
                                        {{ post.reactions_count }}
                                    </span>
                                    <span
                                        class="flex items-center text-sm text-muted-foreground gap-1"
                                    >
                                        <BookmarkIcon class="w-3 h-3" />
                                        {{ post.bookmarks_count }}
                                    </span>
                                    <span
                                        class="flex items-center text-sm text-muted-foreground gap-1"
                                    >
                                        <MessageCircleIcon class="w-3 h-3" />
                                        {{ post.comments_count }}
                                    </span>
                                </div>
                                <!--                                <p class="text-xs text-muted-foreground"-->
                                <!--                                  -->
                                <!--                                >-->
                                <!--                                    {{ formatDistanceToNowStrict(post.created_at, { addSuffix: false }) }} ago-->
                                <!--                                </p-->
                                <!--                                >-->
                            </div>
                        </div>
                        <img
                            class="max-h-14 w-14 lg:max-w-16 lg:h-16 ml-auto object-cover rounded-md"
                            v-if="post.cover_image_url"
                            :src="post.cover_image_url"
                            alt=""
                        />
                    </div>
                    <Separator v-if="posts.length - 1 !== index" />
                </template>
            </div>
        </CardContent>
    </Card>
</template>
