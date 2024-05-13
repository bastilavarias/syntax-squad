<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { Construction } from "lucide-vue-next";
</script>

<template>
    <Card>
        <CardHeader class="py-4">
            <CardTitle class="text-xl">What is SyntaxSquad?</CardTitle>
        </CardHeader>
        <Separator></Separator>
        <CardContent class="pt-4 flex justify-center">
            <Construction :size="120" />
        </CardContent>
    </Card>
</template>
