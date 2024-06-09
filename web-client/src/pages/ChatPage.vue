<script setup lang="ts">
import CustomSideBar from "@/components/CustomSideBar.vue";
import RecentPostsCard from "@/components/RecentPostsCard.vue";
import SyntaxSquadAboutCard from "@/components/SyntaxSquadAboutCard.vue";
import PageContainer from "@/components/PageContainer.vue";
</script>

<template>
    <PageContainer>
        <div class="grid grid-cols-12 lg:gap-5">
            <div class="col-span-2 max-lg:hidden">
                <CustomSideBar />
            </div>
            <div class="col-span-12 lg:col-span-6 bg-black">
                <main class="relative">
                    <RouterView />
                </main>
            </div>
            <div class="col-span-4 max-lg:hidden">
                <div class="space-y-5">
                    <RecentPostsCard />
                    <SyntaxSquadAboutCard />
                </div>
            </div>
        </div>
    </PageContainer>
</template>
