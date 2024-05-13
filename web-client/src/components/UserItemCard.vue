<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
</script>

<template>
    <Card>
        <CardHeader class="py-4">
            <div class="flex justify-between items-start">
                <div class="flex items-center space-x-4">
                    <Avatar>
                        <AvatarImage
                            src="https://avatars.githubusercontent.com/u/24890911?v=4"
                        />
                        <AvatarFallback>OM</AvatarFallback>
                    </Avatar>
                    <div class="space-y-1">
                        <p class="font-medium leading-none text-black">
                            Sebastian Curtis Lavarias
                        </p>
                        <p class="text-xs text-muted-foreground">
                            bastilavarias
                        </p>
                    </div>
                </div>

                <Button>Follow</Button>
            </div>
        </CardHeader>
        <CardContent class="space-y-5">
            <div class="flex space-x-4">
                <div class="flex space-x-4">
                    <p class="text-xs text-muted-foreground">99 Posts</p>
                    <p class="text-xs text-muted-foreground">99 Followers</p>
                    <p class="text-xs text-muted-foreground">99 Following</p>
                </div>
            </div>
            <p class="text-sm">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet
                dolorum enim explicabo labore laborum non officia quas ratione
                velit voluptatibus! Aut beatae dolorem eaque eligendi et ex
                exercitationem, fugit illum ipsa, itaque laboriosam maiores
                nostrum obcaecati officia quia repellendus sed velit! At
                consequatur dolore ea enim eveniet, nisi nostrum provident!
            </p>
        </CardContent>
        <CardContent>
            <p class="text-sm">Latest Posts</p>
        </CardContent>
    </Card>
</template>
