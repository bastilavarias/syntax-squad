<script setup lang="ts">
import { socket, state } from "@/socket";
import { Badge } from "@/components/ui/badge";
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from "@/components/ui/tooltip";
import { computed } from "vue";

const isConnected = computed(() => state.connected);
</script>

<template>
    <TooltipProvider>
        <Tooltip>
            <TooltipTrigger
                ><Badge
                    :class="`${
                        isConnected
                            ? 'bg-green-600 hover:bg-green-600'
                            : 'bg-muted-foreground hover:bg-muted-foreground'
                    }`"
                    >{{ isConnected ? "Connected" : "Disconnected" }}</Badge
                ></TooltipTrigger
            >
            <TooltipContent>
                <p>
                    {{
                        isConnected
                            ? "You're connected to the socket.io server."
                            : "You're disconnected to the socket.io server."
                    }}
                </p>
            </TooltipContent>
        </Tooltip>
    </TooltipProvider>
</template>
