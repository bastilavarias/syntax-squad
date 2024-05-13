<script setup lang="ts">
import RecentPostsCard from "@/components/RecentPostsCard.vue";
import SyntaxSquadAboutCard from "@/components/SyntaxSquadAboutCard.vue";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import PostItemCard from "@/components/PostItemCard.vue";
import { ListPostsPayload, usePostStore } from "@/stores/post.ts";
import { useToast } from "@/components/ui/toast";
import { useRoute, useRouter } from "vue-router";
import { computed, onBeforeMount, ref, watch } from "vue";
import InfiniteLoading from "v3-infinite-loading";
import { useCustomComposable } from "@/custom-composable.ts";

const { toast } = useToast();
const router = useRouter();
const route = useRoute();
const postStore = usePostStore();
const customComposable = useCustomComposable();

const sortBy = ref(route.query.sort ?? "relevance");
const loading = ref(true);
const postsIdentifier = ref(0);
const posts = ref([]);
const page = ref(1);
const perPage = ref(5);

const keyword = computed(() => route.query.keyword);

watch(
    () => keyword.value,
    () => {
        resetPosts();
    }
);

const getPosts = async ($state) => {
    const payload: ListPostsPayload = {
        search: keyword.value,
        page: page.value,
        per_page: perPage.value,
        sort_by: sortBy.value,
        is_draft: 0,
    };
    loading.value = true;
    const result = await postStore.list(payload);
    if (result.success) {
        loading.value = false;
        const _posts = result.data.data;
        posts.value = [...posts.value, ..._posts];
        if (_posts.length >= perPage) {
            $state.loaded();
        } else {
            $state.complete();
        }
        page.value += 1;
        return;
    }
    await router.push({ name: "home-page" });
    toast({
        variant: "destructive",
        title: "Server error.",
        description: result.message,
    });
};
const resetPosts = () => {
    posts.value = [];
    page.value = 1;
    perPage.value += 1;
    postsIdentifier.value += 1;
};

onBeforeMount(() => {
    if (!route.query.keyword) {
        router.go(-1);
    }
});
</script>

<template>
    <div
        class="container flex flex-col lg:flex-row items-start lg:gap-5 h-screen py-10"
    >
        <div class="md:w-2/3">
            <main class="relative pb-10">
                <div class="space-y-3">
                    <Tabs default-value="posts" class="w-full">
                        <TabsList>
                            <TabsTrigger value="posts">
                                Posts related to
                                {{
                                    customComposable.limitString(keyword, 30)
                                }}</TabsTrigger
                            >
                        </TabsList>
                        <TabsContent value="posts">
                            <div class="space-y-3">
                                <template
                                    v-for="(post, index) in posts"
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
                                            <span
                                                class="text-muted-foreground text-xs"
                                                >End of SyntaxSquad searched
                                                posts.</span
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
                <RecentPostsCard />
                <SyntaxSquadAboutCard />
            </div>
        </div>
    </div>
</template>
