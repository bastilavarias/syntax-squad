<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { cn } from "@/lib/utils";
import { ref } from "vue";
import { ScrollArea } from "@/components/ui/scroll-area";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import CardDescription from "@/components/ui/card/CardDescription.vue";
import CardFooter from "@/components/ui/card/CardFooter.vue";
import CustomSideBar from "@/components/CustomSideBar.vue";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { SendIcon } from "lucide-vue-next";

const selectedItem = ref(null);
const isRead = ref(1);
</script>

<template>
    <div class="container flex flex-col lg:flex-row items-start pt-5 h-[91vh]">
        <div class="md:w-2/6 h-full pb-0">
            <Card class="border-0 rounded-none h-full">
                <CardHeader class="h-[8%]">
                    <CardTitle>Chats</CardTitle>
                </CardHeader>
                <CardContent class="h-[92%] px-0">
                    <ScrollArea class="h-full">
                        <div class="flex flex-col gap-2 p-4 pt-0">
                            <TransitionGroup name="list" appear>
                                <button
                                    v-for="n of 5"
                                    :key="n"
                                    :class="
                                        cn(
                                            'flex flex-col items-start gap-2 rounded-lg border p-3 text-left text-sm transition-all hover:bg-accent',
                                            selectedItem === n && 'bg-muted'
                                        )
                                    "
                                    @click="selectedItem = n"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="flex items-center space-x-4"
                                            >
                                                <Avatar>
                                                    <AvatarImage
                                                        src="https://avatars.githubusercontent.com/u/24890911?v=4"
                                                    />
                                                    <AvatarFallback
                                                        >OM</AvatarFallback
                                                    >
                                                </Avatar>
                                                <div class="flex flex-col">
                                                    <div>
                                                        <div
                                                            class="flex space-between"
                                                        >
                                                            <div
                                                                class="font-semibold"
                                                            >
                                                                bastilavarias
                                                            </div>

                                                            <div
                                                                :class="
                                                                    cn(
                                                                        'ml-auto text-xs',
                                                                        selectedItem ===
                                                                            n
                                                                            ? 'text-foreground'
                                                                            : 'text-muted-foreground'
                                                                    )
                                                                "
                                                            >
                                                                3 days ago
                                                            </div>
                                                        </div>
                                                        <span
                                                            v-if="!isRead"
                                                            class="flex h-2 w-2 rounded-full bg-blue-600"
                                                        />
                                                    </div>

                                                    <div
                                                        class="line-clamp-2 text-xs text-muted-foreground"
                                                    >
                                                        You: Lorem ipsum dolor
                                                        sit amet, consectetur
                                                        adipisicing elit. Amet
                                                        aperiam beatae, earum,
                                                        enim esse hic labore
                                                        nesciunt obcaecati
                                                        officiis praesentium
                                                        quasi quibusdam
                                                        suscipit, vero. Atque
                                                        commodi distinctio
                                                        facilis ratione
                                                        voluptates!
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </TransitionGroup>
                        </div>
                    </ScrollArea>
                </CardContent>
            </Card>
        </div>
        <div class="md:w-2/3 h-full pb-0">
            <Card class="border-0 rounded-none h-full relative">
                <div class="h-[10%] px-6 flex items-center">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex items-center space-x-4">
                                <Avatar>
                                    <AvatarImage
                                        src="https://avatars.githubusercontent.com/u/24890911?v=4"
                                    />
                                    <AvatarFallback>OM</AvatarFallback>
                                </Avatar>
                                <div class="flex justify-center flex-col">
                                    <div>
                                        <div class="flex space-between">
                                            <div class="font-semibold">
                                                bastilavarias
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <ScrollArea class="h-[80%]">
                    <CardContent class="py-6 space-y-3">
                        <template v-for="n in 5" :key="n">
                            <div class="lg:max-w-[60%]">
                                <div class="flex space-x-1 items-start">
                                    <Avatar class="w-6 h-6">
                                        <AvatarImage
                                            src="https://avatars.githubusercontent.com/u/24890911?v=4"
                                        />
                                        <AvatarFallback>OM</AvatarFallback>
                                    </Avatar>
                                    <div
                                        class="bg-gray-50 rounded p-2 text-sm font-light space-y-2"
                                    >
                                        <small class="text-xs"
                                            >Sebastian sent 3 days ago</small
                                        >
                                        <p>
                                            Lorem ipsum dolor sit amet,
                                            consectetur adipisicing elit.
                                            Accusantium adipisci aperiam autem
                                            commodi dolore ducimus enim, ex
                                            excepturi exercitationem fugiat
                                            magnam, molestiae, nemo nesciunt
                                            perferendis quae quaerat quasi quia
                                            ratione repellat sed! Atque, culpa
                                            exercitationem expedita impedit
                                            iusto pariatur quaerat quam totam
                                            veritatis vero. Accusantium iure
                                            modi odio optio ut? ...
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <div></div>
                                <div class="lg:max-w-[60%] spacy-2">
                                    <div class="flex space-x-1 items-start">
                                        <div
                                            class="bg-gray-50 rounded p-2 text-sm font-light"
                                        >
                                            <p>
                                                Lorem ipsum dolor sit amet,
                                                consectetur adipisicing elit.
                                                Accusantium adipisci aperiam
                                                autem commodi dolore ducimus
                                                enim, ex excepturi
                                                exercitationem fugiat magnam,
                                                molestiae, nemo nesciunt
                                                perferendis quae quaerat quasi
                                                quia ratione repellat sed!
                                                Atque, culpa exercitationem
                                                expedita impedit iusto pariatur
                                                quaerat quam totam veritatis
                                                vero. Accusantium iure modi odio
                                                optio ut? ...
                                            </p>
                                            <div class="flex justify-between">
                                                <div></div>
                                                <small class="text-xs"
                                                    >You sent 3 days ago</small
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </CardContent>
                </ScrollArea>
                <CardFooter
                    class="h-[10%] absolute bottom-0 left-0 w-full space-x-1"
                >
                    <Input placeholder="Type a message here..." />
                    <Button size="icon" variant="ghost">
                        <SendIcon />
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
