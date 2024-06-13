<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { cn } from "@/lib/utils";
import { Badge } from "@/components/ui/badge";
import { formatDistanceToNow } from "date-fns";
import PostUserItem from "@/components/PostUserItem.vue";

defineProps(["post"]);
</script>

<template>
    <Card
        :class="
            cn(
                'pb-0 overflow-hidden rounded-none shadow-none border-0 md:rounded-lg md:shadow-sm md:border',
                $attrs.class ?? '',
            )
        "
    >
        <img
            class="w-full h-80 object-cover"
            :src="post.cover_image_url"
            alt=""
            v-if="post.cover_image_url"
        />
        <CardContent class="space-y-5 pt-4">
            <div class="flex flex-col space-y-2 md:space-y-3">
                <router-link
                    :to="{
                        name: 'view-post-page',
                        params: {
                            username: post.user.username,
                            slug: post.slug,
                        },
                    }"
                >
                    <p class="text-2xl md:text-3xl font-bold">
                        {{ post.title }}
                    </p>
                </router-link>
                <div class="flex gap-1">
                    <template v-for="(tag, index) in post.tags" :key="index">
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
            </div>
            <PostUserItem
                :user="post.user"
                :created-at="post.created_at"
                :reactions="post.reactions_count"
                :comments="post.comments_count"
                :bookmarks="post.bookmarks_count"
            />
        </CardContent>
        <template v-if="post.comments_count > 0">
            <CardContent class="space-y-3">
                <template
                    v-for="(comment, index) in post.comments"
                    :key="index"
                >
                    <div class="flex space-x-1 items-start">
                        <Avatar class="w-6 h-6">
                            <AvatarImage :src="comment.user.avatar_url" />
                            <AvatarFallback
                                >{{
                                    comment.user.name
                                        ? comment.user.name[0]
                                        : comment.user.username[0]
                                }}
                            </AvatarFallback>
                        </Avatar>
                        <div
                            class="bg-gray-50 rounded p-2 text-sm font-light space-y-2 w-full"
                        >
                            <div>
                                {{ comment.user.name || comment.user.username }}
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
                <Button
                    variant="ghost"
                    class="font-light text-xs"
                    v-if="post.comments_count > 3"
                    >See all {{ post.comments_count }} comments
                </Button>
            </CardContent>
        </template>
    </Card>
</template>
