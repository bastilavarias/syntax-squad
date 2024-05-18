<script setup lang="ts">
import CustomSideBar from "@/components/CustomSideBar.vue";
import RecentPostsCard from "@/components/RecentPostsCard.vue";
import SyntaxSquadAboutCard from "@/components/SyntaxSquadAboutCard.vue";
</script>

<template>
    <div
        class="container flex flex-col lg:flex-row items-start lg:gap-5 h-screen pt-10"
    >
        <div class="md:w-2/12">
            <CustomSideBar />
        </div>
        <div class="md:w-1/2 h-full">
            <main class="relative pb-10 h-full">
                <RouterView />
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
