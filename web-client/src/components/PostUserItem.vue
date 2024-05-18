<script setup lang="ts">
import { format, formatDistanceToNow, isBefore, subDays } from "date-fns";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { BookmarkIcon, HeartIcon, MessageCircleIcon } from "lucide-vue-next";
import { Button } from "@/components/ui/button";
import { useCustomComposable } from "@/custom-composable.ts";

defineProps(["createdAt", "user", "reactions", "comments", "bookmarks"]);

const customComposable = useCustomComposable();
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between space-x-4">
            <div class="flex items-center space-x-4">
                <Avatar>
                    <AvatarImage :src="user.avatar_url" />
                    <AvatarFallback>{{ user.name[0] }}</AvatarFallback>
                </Avatar>
                <div class="space-y-1">
                    <router-link
                        :to="{
                            name: 'profile-page',
                            params: { username: user.username },
                        }"
                    >
                        <p
                            class="font-medium leading-none text-black hover:underline hover:cursor-pointer"
                        >
                            {{ customComposable.limitString(user.name, 70) }}
                        </p>
                    </router-link>
                    <p class="text-xs text-muted-foreground">
                        Posted on
                        {{ format(new Date(createdAt), "MMM dd yy") }}
                        {{
                            isBefore(
                                new Date(createdAt),
                                subDays(new Date(), 3)
                            )
                                ? `(${formatDistanceToNow(
                                      new Date(createdAt)
                                  )})`
                                : ""
                        }}
                    </p>
                </div>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <div class="flex items-center justify-between">
                <Button variant="ghost" class="flex items-center space-x-1">
                    <HeartIcon
                        color="#e24155"
                        :stroke-width="3"
                        class="w-4 h-4"
                    />
                    <span class="text-xs">{{ reactions || 0 }} reactions</span>
                </Button>
                <Button variant="ghost" class="flex items-center space-x-1">
                    <MessageCircleIcon class="w-4 h-4" />
                    <span class="text-xs">{{ comments || 0 }} comments</span>
                </Button>
            </div>
            <Button variant="ghost" class="flex items-center space-x-1">
                <BookmarkIcon class="w-4 h-4" />
                <span class="text-xs">{{ bookmarks || 0 }}</span>
            </Button>
        </div>
    </div>
</template>
