<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { useAuthStore } from "@/stores/auth.ts";
import { ref, computed, watch } from "vue";
import { ListPostsPayload, usePostStore } from "@/stores/post.ts";
import { useToast } from "@/components/ui/toast";
import CustomLoadingSpinner from "@/components/CustomLoadingSpinner.vue";
import {
    BookmarkIcon,
    HeartIcon,
    MessageCircleIcon,
    EllipsisVertical,
} from "lucide-vue-next";
import { useCustomComposable } from "@/custom-composable.ts";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Button } from "@/components/ui/button";
import router from "@/router.ts";
import { formatDistanceToNow } from "date-fns";
import { Badge } from "@/components/ui/badge";
import {
    Pagination,
    PaginationEllipsis,
    PaginationList,
    PaginationListItem,
} from "@/components/ui/pagination";
import { cn } from "@/lib/utils.ts";

const props = defineProps(["simple"]);

const authStore = useAuthStore();
const postStore = usePostStore();
const { toast } = useToast();
const customComposable = useCustomComposable();

const currPage = ref(1);
const perPage = ref(5);
const sortBy = ref("touched");
const listPostsLoading = ref(true);
const posts = ref([]);
const postsCount = ref(0);
const deletePostLoading = ref(false);

const user = computed(() =>
    authStore.isAuthenticated ? authStore.user : null,
);

watch(
    () => currPage.value,
    async () => await getPostList(),
);

const getPostList = async () => {
    listPostsLoading.value = true;
    posts.value = [];
    const payload: ListPostsPayload = {
        page: currPage.value,
        per_page: perPage.value,
        user_id: user.value.id,
        sort_by: sortBy.value,
    };
    const result = await postStore.list(payload);
    if (result.success) {
        postsCount.value = result.data.total || 0;
        posts.value = result.data.data;
        listPostsLoading.value = false;
        return;
    }
    await router.push({ name: "home-page" });
    toast({
        variant: "destructive",
        title: "Server error.",
        description: result.message,
    });
};
const onGoEditPage = (postID) => {
    router.push({
        name: "post-form-page",
        params: { operation: "edit" },
        query: { postID },
    });
};
const onGoPostPage = (post) => {
    router.push({
        name: "view-post-page",
        params: { username: post.user.username, slug: post.slug },
    });
};
const onDeletePost = async (postID: number) => {
    deletePostLoading.value = true;
    const result = await postStore.delete(postID);
    if (result.success) {
        await getPostList();
        toast({
            title: "Post successfully deleted",
            description:
                "Your post has been successfully deleted. You will be redirected to the create form shortly.",
        });
        await router.push({
            name: "post-form-page",
            params: { operation: "new" },
        });
        return;
    }
    deletePostLoading.value = false;
    toast({
        variant: "destructive",
        title: "Server error.",
        description: result.message,
    });
};

getPostList();
</script>

<template>
    <Card
        :class="
            cn(
                '',
                props.simple
                    ? 'max-h-[500px] shadow-none border-0 overflow-auto'
                    : 'min-h-[500px]',
            )
        "
    >
        <template v-if="listPostsLoading">
            <div class="h-[500px] bg-gray-50 flex justify-center items-center">
                <CustomLoadingSpinner class="h-5 w-5" />
            </div>
        </template>
        <template v-else>
            <CardHeader class="flex justify-between" v-if="!simple">
                <CardTitle class="text-xl">Your posts</CardTitle>
            </CardHeader>
            <CardContent
                :class="
                    cn('', props.simple ? 'shadow-0 border-0 px-0 pt-4' : '')
                "
            >
                <div class="space-y-3">
                    <template v-for="(post, index) in posts" :key="post.id">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <router-link
                                    :to="{
                                        name: 'post-form-page',
                                        params: { operation: 'edit' },
                                        query: { postID: post.id },
                                    }"
                                >
                                    <p
                                        class="text-sm mr-1 font-medium leading-none hover:underline hover:cursor-pointer"
                                    >
                                        <Badge
                                            variant="outline"
                                            v-if="post.is_draft"
                                            >Unpublished</Badge
                                        >
                                        {{
                                            customComposable.limitString(
                                                post.title,
                                                70,
                                            )
                                        }}
                                    </p>
                                </router-link>
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button size="icon" variant="ghost">
                                            <EllipsisVertical />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent
                                        class="w-56"
                                        align="end"
                                    >
                                        <DropdownMenuLabel
                                            class="font-normal flex"
                                        >
                                            {{
                                                customComposable.limitString(
                                                    post.title,
                                                    50,
                                                )
                                            }}
                                        </DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuGroup>
                                            <DropdownMenuItem
                                                @click="onGoPostPage(post)"
                                                v-if="!post.is_draft"
                                            >
                                                View as post
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                @click="onGoEditPage(post.id)"
                                            >
                                                Edit post
                                            </DropdownMenuItem>
                                        </DropdownMenuGroup>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem
                                            class="text-red-600"
                                            @click="onDeletePost(post.id)"
                                        >
                                            <template v-if="deletePostLoading">
                                                <CustomLoadingSpinner
                                                    class="mr-2 w-4 h-4"
                                                />
                                            </template>
                                            Delete post
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex justify-start space-x-2">
                                    <span
                                        class="flex items-center text-sm text-muted-foreground"
                                    >
                                        <HeartIcon class="w-3 h-3" />
                                        {{ post.reactions_count }}
                                    </span>
                                    <span
                                        class="flex items-center text-sm text-muted-foreground"
                                    >
                                        <BookmarkIcon class="w-3 h-3" />
                                        {{ post.bookmarks_count }}
                                    </span>
                                    <span
                                        class="flex items-center text-sm text-muted-foreground"
                                    >
                                        <MessageCircleIcon class="w-3 h-3" />
                                        {{ post.comments_count }}
                                    </span>
                                </div>
                                <span class="text-xs text-muted-foreground"
                                    >Updated
                                    {{ formatDistanceToNow(post.updated_at) }}
                                    ago</span
                                >
                            </div>
                            <Separator v-if="posts.length - 1 !== index" />
                        </div>
                    </template>
                </div>
                <div class="pt-5 flex justify-center">
                    <Pagination
                        v-slot="{ page }"
                        :total="postsCount * 2"
                        :show-edges="false"
                        :sibling-count="2"
                        v-model:page="currPage"
                    >
                        <PaginationList
                            v-slot="{ items }"
                            class="flex items-center gap-1"
                        >
                            <template v-for="(item, index) in items">
                                <PaginationListItem
                                    v-if="item.type === 'page'"
                                    :key="index"
                                    :value="item.value"
                                    as-child
                                >
                                    <Button
                                        class="w-10 h-10 p-0"
                                        :variant="
                                            item.value === page
                                                ? 'default'
                                                : 'outline'
                                        "
                                    >
                                        {{ item.value }}
                                    </Button>
                                </PaginationListItem>
                                <PaginationEllipsis
                                    v-else
                                    :key="item.type"
                                    :index="index"
                                />
                            </template>
                        </PaginationList>
                    </Pagination>
                </div>
            </CardContent>
        </template>
    </Card>
</template>
