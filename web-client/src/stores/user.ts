import { defineStore } from "pinia";
import apiClient from "@/lib/api-client";

export interface UserPayload {
    user_id?: number;
    username?: number;
}

export const userUserStore = defineStore("user", {
    actions: {
        async getByUsername(payload: UserPayload) {
            try {
                const response = await apiClient.get({
                    route: `post/api/user/${payload.username}`,
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },
    },
});
