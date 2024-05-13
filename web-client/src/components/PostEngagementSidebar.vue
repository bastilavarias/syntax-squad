<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { BookmarkIcon, HeartIcon, MessageCircleIcon } from "lucide-vue-next";
import { onMounted, ref } from "vue";
import { usePostReactionStore } from "@/stores/postReaction.ts";
import { usePostBookmarkStore } from "@/stores/postBookmark.ts";

const props = defineProps(["id"]);

const postReaction = usePostReactionStore();
const postBookmark = usePostBookmarkStore();

const reactions = defineModel("reactions", {
    default: 0,
    required: true,
});
const comments = defineModel("comments", {
    default: 0,
    required: true,
});
const bookmarks = defineModel("bookmarks", {
    default: 0,
    required: true,
});
const ready = defineModel("ready", {
    default: false,
    required: true,
});
const reactionLoading = ref(false);
const bookmarkLoading = ref(false);
const hasReactionSubscription = ref(false);
const hasBookmarkSubscription = ref(false);

const onClickReaction = async () => {
    reactionLoading.value = true;
    const payload = {
        post_id: props.id,
        name: "heart",
    };
    let result;
    if (hasReactionSubscription.value) {
        hasReactionSubscription.value = false;
        reactions.value -= 1;
        result = await postReaction.delete(payload);
    } else {
        hasReactionSubscription.value = true;
        reactions.value += 1;
        result = await postReaction.create(payload);
    }
    reactionLoading.value = false;
    if (result.success) {
        return;
    }
};
const onCheckReaction = async () => {
    const payload = {
        post_id: props.id,
        name: "heart",
    };
    const result = await postReaction.check(payload);
    if (result.success) {
        hasReactionSubscription.value = result.data || false;
        return;
    }
};
const onClickBookmark = async () => {
    bookmarkLoading.value = true;
    const payload = {
        post_id: props.id,
    };
    let result;
    if (hasBookmarkSubscription.value) {
        hasBookmarkSubscription.value = false;
        bookmarks.value -= 1;
        result = await postBookmark.delete(payload);
    } else {
        hasBookmarkSubscription.value = true;
        bookmarks.value += 1;
        result = await postBookmark.create(payload);
    }
    bookmarkLoading.value = false;
    if (result.success) {
        return;
    }
};
const onCheckBookmark = async () => {
    const payload = {
        post_id: props.id,
    };
    const result = await postBookmark.check(payload);
    if (result.success) {
        hasBookmarkSubscription.value = result.data || false;
        return;
    }
};

onMounted(async () => {
    try {
        await onCheckReaction();
        await onCheckBookmark();
        ready.value = true;
    } catch (_) {}
});
</script>

<template>
    <div class="flex flex-col space-y-2">
        <div class="flex flex-col justify-center items-center">
            <Button
                variant="ghost"
                size="icon"
                class="hover:text-[#e24155]"
                :class="{ 'text-[#e24155]': hasReactionSubscription }"
                :disabled="reactionLoading"
                @click="onClickReaction"
            >
                <HeartIcon
                    :fill="hasReactionSubscription ? '#e24155' : 'none'"
                />
            </Button>
            <span class="text-sm font-light"> {{ reactions }} </span>
        </div>
        <div class="flex flex-col justify-center items-center">
            <Button variant="ghost" size="icon" class="hover:text-[#ea8000]">
                <MessageCircleIcon />
            </Button>
            <span class="text-sm font-light"> {{ comments }} </span>
        </div>

        <div class="flex flex-col justify-center items-center">
            <Button
                variant="ghost"
                size="icon"
                class="hover:text-[#9400ff]"
                :class="{ 'text-[#9400ff]': hasBookmarkSubscription }"
                :disabled="bookmarkLoading"
                @click="onClickBookmark"
            >
                <BookmarkIcon
                    :fill="hasBookmarkSubscription ? '#9400ff' : 'none'"
                />
            </Button>
            <span class="text-sm font-light"> {{ bookmarks }} </span>
        </div>
    </div>
</template>
