import { reactive } from "vue";
import { io } from "socket.io-client";

export const state = reactive({
    connected: false,
});

const URL =
    process.env.NODE_ENV === "production"
        ? undefined
        : import.meta.env.VITE_SOCKET_IO_ENDPOINT;

export const socket = io(URL, {
    reconnection: true,
    autoConnect: false,
});

socket.on("connect", () => {
    state.connected = true;
});

socket.on("disconnect", () => {
    state.connected = false;
});
