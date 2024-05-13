<script setup lang="ts">
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { ArrowUp, Clock, Flame } from "lucide-vue-next";

const model = defineModel();
</script>

<template>
    <Select v-model="model">
        <SelectTrigger class="w-[180px]">
            <SelectValue placeholder="Select sorting" />
        </SelectTrigger>
        <SelectContent>
            <SelectGroup>
                <SelectLabel>Sort by</SelectLabel>
                <SelectItem value="relevance">
                    <div class="flex items-center">
                        Relevance <Flame class="w-4 h-4 ml-1" />
                    </div>
                </SelectItem>
                <SelectItem value="latest">
                    <div class="flex items-center">
                        Latest <Clock class="w-4 h-4 ml-1" />
                    </div>
                </SelectItem>
                <SelectItem value="top">
                    <div class="flex items-center">
                        Top <ArrowUp class="w-4 h-4 ml-1" />
                    </div>
                </SelectItem>
            </SelectGroup>
        </SelectContent>
    </Select>
</template>
