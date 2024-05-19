import { defineStore } from "pinia";
import apiClient from "@/lib/api-client";

export interface GetChatRoomPayload {
    room_id?: number;
}
export interface CreateChatRoomPayload {
    message?: string;
    member_id?: number;
}
export interface CreateChatPayload {
    message?: string;
    room_id?: number;
}
export interface ReadChatPayload {
    is_read?: number;
    message_id?: number;
    _method?: string;
}
export interface ListChatsPayload {
    page?: number;
    per_page?: number;
}
export interface ListChatMessagesPayload {
    page?: number;
    per_page?: number;
    room_id: number;
}

export const useChatStore = defineStore("chat", {
    state: () => ({
        room: {
            value: [],
            page: 1,
            perPage: 5,
        },
    }),

    actions: {
        async create(payload: CreateChatPayload) {
            try {
                const response = await apiClient.post({
                    route: "chat/api/conversation",
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async list(payload: ListChatsPayload) {
            try {
                const parameters = apiClient.toURLSearchParams(payload);
                const response = await apiClient.get({
                    route: `chat/api/room?${parameters}`,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async get(payload: ListChatMessagesPayload) {
            try {
                const parameters = apiClient.toURLSearchParams(payload);
                const response = await apiClient.get({
                    route: `chat/api/conversation/${payload.room_id}?${parameters}`,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async getRoom(payload: GetChatRoomPayload) {
            try {
                const response = await apiClient.get({
                    route: `chat/api/room/${payload.room_id}`,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async read(payload: ReadChatPayload) {
            try {
                const response = await apiClient.put({
                    route: `chat/api/conversation/read/${payload.message_id}`,
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async createRoom(payload: CreateChatRoomPayload) {
            try {
                const response = await apiClient.post({
                    route: "chat/api/room",
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },
    },
});
