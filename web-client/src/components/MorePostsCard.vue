<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { ref } from "vue";
import { ListPostsPayload, usePostStore } from "@/stores/post.ts";
import { useToast } from "@/components/ui/toast";
import { useCustomComposable } from "@/custom-composable.ts";
import { formatDistanceToNow } from "date-fns";
import { BookmarkIcon, HeartIcon, MessageCircleIcon } from "lucide-vue-next";
import { Badge } from "@/components/ui/badge";

const props = defineProps(["exclude", "userId"]);

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
        user_id: props.userId,
    };
    const result = await postStore.list(payload);
    if (result.success) {
        posts.value = result.data.data;
        if (props.exclude.length > 0) {
            posts.value = posts.value.filter(
                (post) => !props.exclude.includes(post.id)
            );
        }
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
            <CardTitle class="text-xl">More from this Author</CardTitle>
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
                    <div class="space-y-3">
                        <div class="flex justify-between">
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
                                    class="text-sm mr-1 font-medium leading-none hover:underline hover:cursor-pointer"
                                >
                                    {{
                                        customComposable.limitString(
                                            post.title,
                                            70
                                        )
                                    }}
                                </p>
                            </router-link>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex justify-start space-x-2">
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
                                                class="font-light lowercase text-xs hover:cursor-pointer hover:underline"
                                            >
                                                {{ tag.name }}
                                            </span>
                                        </router-link>
                                    </Badge>
                                </template>
                            </div>
                            <span class="text-xs text-muted-foreground"
                                >Posted
                                {{ formatDistanceToNow(post.created_at) }}
                                ago</span
                            >
                        </div>
                        <Separator v-if="posts.length - 1 !== index" />
                    </div>
                </template>
            </div>
        </CardContent>
    </Card>
</template>
