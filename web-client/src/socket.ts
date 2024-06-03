import { reactive } from "vue";
import { io } from "socket.io-client";

export const state = reactive({
    connected: false,
});

const URL =
    process.env.NODE_ENV === "production"
        ? undefined
        : import.meta.env.VITE_BASE_API_ENDPOINT;

export const socket = io(URL, {
    reconnection: true,
    autoConnect: false,
    path: "/chat-service/",
});

socket.on("connect", () => {
    state.connected = true;
});

socket.on("disconnect", () => {
    state.connected = false;
});
