<script setup lang="ts">
import RecentPostsCard from "@/components/RecentPostsCard.vue";
import SyntaxSquadAboutCard from "@/components/SyntaxSquadAboutCard.vue";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { computed, ref, watch } from "vue";
import { useToast } from "@/components/ui/toast";
import { userUserStore } from "@/stores/user.ts";
import { useRoute } from "vue-router";
import { format, formatDistanceToNow, isBefore, subDays } from "date-fns";
import {
    ListUserFollowerPayload,
    useUserFollowerStore,
} from "@/stores/userFollower.ts";
import { useAuthStore } from "@/stores/auth.ts";
import CustomLoadingSpinner from "@/components/CustomLoadingSpinner.vue";
import { ListPostsPayload, usePostStore } from "@/stores/post.ts";
import InfiniteLoading from "v3-infinite-loading";
import PostItemCard from "@/components/PostItemCard.vue";
import {
    ListBookmarksPayload,
    usePostBookmarkStore,
} from "@/stores/postBookmark.ts";
import MiniProfileCard from "@/components/MiniProfileCard.vue";
import { GithubIcon } from "lucide-vue-next";
import MessageComposerDialog from "@/components/MessageComposerDialog.vue";

const { toast } = useToast();
const userStore = userUserStore();
const route = useRoute();
const postStore = usePostStore();
const authStore = useAuthStore();
const postBookmarkStore = usePostBookmarkStore();
const userFollowerStore = useUserFollowerStore();

const loading = ref(true);
const user = ref(null);
const isPageReady = ref(false);
const alreadyFollowed = ref(false);
const followerLoading = ref(true);
const followersCountLocal = ref(0);
const tab = ref("posts");
const postContent = ref({
    value: [],
    page: 1,
    perPage: 5,
    sortBy: "latest",
});
const postsIdentifier = ref(0);
const bookmarkContent = ref({
    value: [],
    page: 1,
    perPage: 5,
});
const bookmarksIdentifier = ref(0);
const followerContent = ref({
    value: [],
    page: 1,
    perPage: 5,
});
const followersIdentifier = ref(0);
const followingContent = ref({
    value: [],
    page: 1,
    perPage: 5,
});
const followingsIdentifier = ref(0);
const shouldShowDialog = ref(false);

const username = computed(() => route.params.username || null);
const authuser = computed(() =>
    authStore.isAuthenticated ? authStore.user : null
);
const isSelf = computed(() =>
    user.value
        ? authStore.isAuthenticated
            ? user.value.id === authuser.value.id
            : false
        : false
);

watch(
    () => route.params.username,
    async () => {
        isPageReady.value = false;
        user.value = null;
        followersCountLocal.value = 0;
        alreadyFollowed.value = false;
        tab.value = "posts";
        await getUser();
        resetPosts();
        resetBookmarks();
        resetFollowers();
        resetFollowings();
    }
);
watch(
    () => user.value,
    async (value) => {
        if (value && authStore.isAuthenticated) {
            await checkFollowers();
        }
    }
);

const getUser = async () => {
    loading.value = true;
    const result = await userStore.getByUsername({ username: username.value });
    if (result.success) {
        user.value = Object.assign({}, result.data);
        followersCountLocal.value = user.value.followers_count;
        isPageReady.value = true;
        loading.value = false;
        return;
    }
    toast({
        title: "Something went wrong.",
        description: result.message,
    });
};
const onFollow = async () => {
    followerLoading.value = true;
    const payload = {
        user_id: user.value.id,
    };
    let result;
    if (alreadyFollowed.value) {
        alreadyFollowed.value = false;
        followersCountLocal.value -= 1;
        result = await userFollowerStore.delete(payload);
    } else {
        alreadyFollowed.value = true;
        followersCountLocal.value += 1;
        result = await userFollowerStore.create(payload);
    }
    followerLoading.value = false;
    if (result.success) {
        return;
    }
};
const checkFollowers = async () => {
    const payload = {
        user_id: user.value.id,
    };
    const result = await userFollowerStore.check(payload);
    if (result.success) {
        alreadyFollowed.value = result.data || false;
        followerLoading.value = false;
        return;
    }
};
const getPosts = async ($state) => {
    const payload: ListPostsPayload = {
        page: postContent.value.page,
        per_page: postContent.value.perPage,
        sort_by: postContent.value.sortBy,
        user_id: user.value.id,
        is_draft: 0,
    };
    loading.value = true;
    const result = await postStore.list(payload);
    if (result.success) {
        loading.value = false;
        const _posts = result.data.data;
        postContent.value.value = [...postContent.value.value, ..._posts];
        if (_posts.length >= postContent.value.perPage) {
            $state.loaded();
        } else {
            $state.complete();
        }
        postContent.value.page += 1;
        return;
    }
    toast({
        variant: "destructive",
        title: "Server error.",
        description: result.message,
    });
};
const resetPosts = () => {
    postContent.value.value = [];
    postContent.value.page = 1;
    postContent.value.perPage += 1;
    postsIdentifier.value += 1;
};
const getBookmarks = async ($state) => {
    const payload: ListBookmarksPayload = {
        page: bookmarkContent.value.page,
        per_page: bookmarkContent.value.perPage,
        user_id: user.value.id,
    };
    loading.value = true;
    const result = await postBookmarkStore.list(payload);
    if (result.success) {
        loading.value = false;
        const _postBookmarks = result.data.data;
        bookmarkContent.value.value = [
            ...bookmarkContent.value.value,
            ..._postBookmarks,
        ];
        if (_postBookmarks.length >= bookmarkContent.value.perPage) {
            $state.loaded();
        } else {
            $state.complete();
        }
        bookmarkContent.value.page += 1;
        return;
    }
    toast({
        variant: "destructive",
        title: "Server error.",
        description: result.message,
    });
};
const resetBookmarks = () => {
    bookmarkContent.value.value = [];
    bookmarkContent.value.page = 1;
    bookmarkContent.value.perPage += 1;
    bookmarksIdentifier.value += 1;
};
const getFollowers = async ($state) => {
    const payload: ListUserFollowerPayload = {
        page: followerContent.value.page,
        per_page: followerContent.value.perPage,
        user_id: user.value.id,
        filter_by: "follower",
    };
    loading.value = true;
    const result = await userFollowerStore.list(payload);
    if (result.success) {
        loading.value = false;
        const _userFollowers = result.data.data;
        followerContent.value.value = [
            ...followerContent.value.value,
            ..._userFollowers,
        ];
        if (_userFollowers.length >= followerContent.value.perPage) {
            $state.loaded();
        } else {
            $state.complete();
        }
        followerContent.value.page += 1;
        return;
    }
    toast({
        variant: "destructive",
        title: "Server error.",
        description: result.message,
    });
};
const resetFollowers = () => {
    followerContent.value.value = [];
    followerContent.value.page = 1;
    followerContent.value.perPage += 1;
    followersIdentifier.value += 1;
};
const getFollowings = async ($state) => {
    const payload: ListUserFollowerPayload = {
        page: followingContent.value.page,
        per_page: followingContent.value.perPage,
        user_id: user.value.id,
        filter_by: "following",
    };
    loading.value = true;
    const result = await userFollowerStore.list(payload);
    if (result.success) {
        loading.value = false;
        const _userFollowings = result.data.data;
        followingContent.value.value = [
            ...followingContent.value.value,
            ..._userFollowings,
        ];
        if (_userFollowings.length >= followingContent.value.perPage) {
            $state.loaded();
        } else {
            $state.complete();
        }
        followingContent.value.page += 1;
        return;
    }
    toast({
        variant: "destructive",
        title: "Server error.",
        description: result.message,
    });
};
const resetFollowings = () => {
    followingContent.value.value = [];
    followingContent.value.page = 1;
    followingContent.value.perPage += 1;
    followingsIdentifier.value += 1;
};

getUser();
</script>

<template>
    <div
        class="container flex flex-col lg:flex-row items-start lg:gap-5 h-screen pt-10"
    >
        <template v-if="!isPageReady">
            <div class="flex justify-center w-full">
                <img class="w-auto h-40" src="/nyan-cat.gif" alt="Auth GIF" />
            </div>
        </template>
        <template v-else>
            <div class="md:w-2/3">
                <main class="relative pb-10">
                    <div class="space-y-3">
                        <div
                            class="flex items-center justify-between space-x-4"
                        >
                            <div class="flex items-center space-x-4">
                                <Avatar size="lg">
                                    <AvatarImage :src="user.avatar_url" />
                                    <AvatarFallback>{{
                                        user.name[0]
                                    }}</AvatarFallback>
                                </Avatar>
                                <div class="space-y-2">
                                    <h1
                                        class="text-2xl font-bold leading-none text-black"
                                    >
                                        {{ user.name }}
                                    </h1>
                                    <div>
                                        <a
                                            class="text-muted-foreground hover:underline hover:cursor-pointer flex items-center space-x-1"
                                            target="_blank"
                                            :href="`https://github.com/${user.username}`"
                                        >
                                            @{{ user.username }} <GithubIcon />
                                        </a>
                                        <p class="text-muted-foreground">
                                            {{ followersCountLocal }} followers
                                            &
                                            {{ user.followings_count }}
                                            following
                                        </p>
                                        <p class="text-muted-foreground">
                                            Joined
                                            {{
                                                format(
                                                    new Date(user.created_at),
                                                    "MMM dd yy"
                                                )
                                            }}
                                            {{
                                                isBefore(
                                                    new Date(user.created_at),
                                                    subDays(new Date(), 3)
                                                )
                                                    ? `(${formatDistanceToNow(
                                                          user.created_at
                                                      )})`
                                                    : ""
                                            }}
                                        </p>
                                        <Button
                                            variant="ghost"
                                            class="text-xs w-auto h-7 underline pl-0"
                                            @click="shouldShowDialog = true"
                                            v-if="
                                                authStore.isAuthenticated &&
                                                !isSelf
                                            "
                                            >Send a Message</Button
                                        >
                                    </div>
                                </div>
                            </div>

                            <Button
                                :variant="
                                    alreadyFollowed ? 'outline' : 'default'
                                "
                                :disabled="followerLoading"
                                @click="onFollow"
                                v-if="authStore.isAuthenticated && !isSelf"
                            >
                                <template v-if="followerLoading">
                                    <CustomLoadingSpinner
                                        class="mr-2 w-4 h-4"
                                    /> </template
                                ><template v-else>{{
                                    alreadyFollowed ? "Unfollow" : "Follow"
                                }}</template></Button
                            >
                        </div>

                        <Tabs class="w-full" v-model="tab">
                            <TabsList>
                                <TabsTrigger value="posts"> Posts </TabsTrigger>
                                <TabsTrigger value="bookmarks">
                                    Bookmarks
                                </TabsTrigger>
                                <TabsTrigger value="followers">
                                    Followers
                                </TabsTrigger>
                                <TabsTrigger value="followings">
                                    Following
                                </TabsTrigger>
                            </TabsList>
                            <TabsContent value="posts">
                                <div class="space-y-3">
                                    <template
                                        v-for="(
                                            post, index
                                        ) in postContent.value"
                                        :key="index"
                                    >
                                        <PostItemCard :post="post" />
                                    </template>
                                    <InfiniteLoading
                                        class="pt-5"
                                        :identifier="postsIdentifier"
                                        @infinite="getPosts"
                                    >
                                        <template #spinner>
                                            <div
                                                class="flex flex-col items-center"
                                            >
                                                <img
                                                    class="w-auto h-40"
                                                    src="/nyan-cat.gif"
                                                    alt="Auth GIF"
                                                />
                                            </div>
                                        </template>
                                        <template #complete>
                                            <div
                                                class="flex flex-col items-center"
                                            >
                                                <span
                                                    class="text-muted-foreground text-xs"
                                                    >End of posts.</span
                                                >
                                            </div>
                                        </template>
                                    </InfiniteLoading>
                                </div>
                            </TabsContent>
                            <TabsContent value="bookmarks">
                                <div class="space-y-3">
                                    <template
                                        v-for="(
                                            bookmark, index
                                        ) in bookmarkContent.value"
                                        :key="index"
                                    >
                                        <PostItemCard :post="bookmark.post" />
                                    </template>
                                    <InfiniteLoading
                                        class="pt-5"
                                        :identifier="bookmarksIdentifier"
                                        @infinite="getBookmarks"
                                    >
                                        <template #spinner>
                                            <div
                                                class="flex flex-col items-center"
                                            >
                                                <img
                                                    class="w-auto h-40"
                                                    src="/nyan-cat.gif"
                                                    alt="Auth GIF"
                                                />
                                            </div>
                                        </template>
                                        <template #complete>
                                            <div
                                                class="flex flex-col items-center"
                                            >
                                                <span
                                                    class="text-muted-foreground text-xs"
                                                    >End of bookmarks.</span
                                                >
                                            </div>
                                        </template>
                                    </InfiniteLoading>
                                </div>
                            </TabsContent>
                            <TabsContent value="followers">
                                <template
                                    v-for="(
                                        follower, index
                                    ) in followerContent.value"
                                    :key="index"
                                >
                                    <MiniProfileCard
                                        :user="follower.follower"
                                    />
                                </template>
                                <div class="space-y-3">
                                    <InfiniteLoading
                                        class="pt-5"
                                        :identifier="followersIdentifier"
                                        @infinite="getFollowers"
                                    >
                                        <template #spinner>
                                            <div
                                                class="flex flex-col items-center"
                                            >
                                                <img
                                                    class="w-auto h-40"
                                                    src="/nyan-cat.gif"
                                                    alt="Auth GIF"
                                                />
                                            </div>
                                        </template>
                                        <template #complete>
                                            <div
                                                class="flex flex-col items-center"
                                            >
                                                <span
                                                    class="text-muted-foreground text-xs"
                                                    >End of followers.</span
                                                >
                                            </div>
                                        </template>
                                    </InfiniteLoading>
                                </div>
                            </TabsContent>
                            <TabsContent value="followings">
                                <template
                                    v-for="(
                                        following, index
                                    ) in followingContent.value"
                                    :key="index"
                                >
                                    <MiniProfileCard
                                        :user="following.following"
                                    />
                                </template>
                                <div class="space-y-3">
                                    <InfiniteLoading
                                        class="pt-5"
                                        :identifier="followingsIdentifier"
                                        @infinite="getFollowings"
                                    >
                                        <template #spinner>
                                            <div
                                                class="flex flex-col items-center"
                                            >
                                                <img
                                                    class="w-auto h-40"
                                                    src="/nyan-cat.gif"
                                                    alt="Auth GIF"
                                                />
                                            </div>
                                        </template>
                                        <template #complete>
                                            <div
                                                class="flex flex-col items-center"
                                            >
                                                <span
                                                    class="text-muted-foreground text-xs"
                                                    >End of followings.</span
                                                >
                                            </div>
                                        </template>
                                    </InfiniteLoading>
                                </div>
                            </TabsContent>
                        </Tabs>
                    </div>
                </main>
            </div>
            <div class="md:w-2/6">
                <div class="space-y-5">
                    <RecentPostsCard :exclude="[]" :user-id="user.id" />
                    <SyntaxSquadAboutCard />
                </div>
            </div>

            <MessageComposerDialog :user="user" v-model="shouldShowDialog" />
        </template>
    </div>
</template>
