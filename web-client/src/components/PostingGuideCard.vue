<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";

const postingGuidelines = [
    {
        title: "Quality over Quantity",
        description:
            "Prioritize quality content over quantity. Focus on delivering well-researched, informative, and engaging posts that provide value to your readers.",
    },
    {
        title: "Proper Formatting",
        description:
            "Format your posts properly with headings, subheadings, bullet points, and paragraphs to improve readability. Use images and multimedia where appropriate to enhance the visual appeal of your content.",
    },
    {
        title: "Promote Ethical Practices",
        description:
            "Avoid engaging in unethical practices such as clickbait titles, misleading information, or deceptive tactics to attract readers. Build trust with your audience through transparency and honesty.",
    },
];
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="text-xl">Posting Guidelines</CardTitle>
        </CardHeader>
        <CardContent>
            <div class="space-y-3">
                <template
                    v-for="(guideline, index) in postingGuidelines"
                    :key="index"
                >
                    <div class="space-y-2">
                        <p class="text-md leading-none">
                            {{ guideline.title }}
                        </p>
                        <p class="text-sm text-muted-foreground">
                            {{ guideline.description }}
                        </p>
                        <Separator
                            v-if="postingGuidelines.length - 1 !== index"
                        />
                    </div>
                </template>
            </div>
        </CardContent>
    </Card>
</template>
