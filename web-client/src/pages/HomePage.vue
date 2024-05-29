<script setup lang="ts">
import PostItemCard from "@/components/PostItemCard.vue";
import CustomSideBar from "@/components/CustomSideBar.vue";
import RecentPostsCard from "@/components/RecentPostsCard.vue";
import SyntaxSquadAboutCard from "@/components/SyntaxSquadAboutCard.vue";
import CustomSortBySelect from "@/components/CustomSortBySelect.vue";
import { ListPostsPayload, usePostStore } from "@/stores/post.ts";
import { useToast } from "@/components/ui/toast";
import { computed, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";

const postStore = usePostStore();
const { toast } = useToast();
const router = useRouter();
const route = useRoute();

const sortBy = ref(route.query.sort ?? "relevance");
const loading = ref(true);
const postsIdentifier = ref(0);

const postContents = computed(() => postStore.content.value);

watch(
    () => sortBy.value,
    async (value) => {
        resetPosts();
        await router.push({ name: "home-page", query: { sort: value } });
    }
);

const getPosts = async ($state) => {
    const payload: ListPostsPayload = {
        page: postStore.content.page,
        per_page: postStore.content.perPage,
        sort_by: sortBy.value,
        is_draft: 0
    };
    loading.value = true;
    const result = await postStore.list(payload);
    if (result.success) {
        loading.value = false;
        const _posts = result.data.data;
        postStore.content.value = [...postStore.content.value, ..._posts];
        if (_posts.length >= postStore.content.perPage) {
            $state.loaded();
        } else {
            $state.complete();
        }
        postStore.content.page += 1;
        return;
    }
    await router.push({ name: "home-page" });
    toast({
        variant: "destructive",
        title: "Server error.",
        description: result.message
    });
};
const resetPosts = () => {
    postStore.content.value = [];
    postStore.content.page = 1;
    postStore.content.perPage += 1;
    postsIdentifier.value += 1;
};
</script>

<template>
    <div
        class="px-4 lg:container h-screen pt-5 lg:pt-10"
    >
        <div class="grid grid-cols-12 lg:gap-5">
            <div class="col-span-2 max-lg:hidden">
                <CustomSideBar />
            </div>
            <div class="col-span-12 lg:col-span-6">
                <main class="relative pb-10">
                    <div class="space-y-3">
                        <CustomSortBySelect v-model="sortBy" />
                        <template
                            v-for="(post, index) in postContents"
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
                                >End of SyntaxSquad community posts.</span
                                >
                                </div>
                            </template>
                        </InfiniteLoading>
                    </div>
                </main>
            </div>
            <div class="col-span-4 max-lg:hidden">
                <div class="space-y-5">
                    <RecentPostsCard />
                    <SyntaxSquadAboutCard />
                </div>
            </div>
        </div>

    </div>
</template>
