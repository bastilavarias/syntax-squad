import { defineStore } from "pinia";
import apiClient from "@/lib/api-client";
import { ListBookmarksPayload } from "@/stores/postBookmark.ts";

export interface UserFollowerPayload {
    user_id?: number;
}
export interface ListUserFollowerPayload {
    page?: number;
    per_page?: number;
    user_id?: number;
    filter_by?: string;
}

export const useUserFollowerStore = defineStore("user-follower", {
    actions: {
        async create(payload: UserFollowerPayload) {
            try {
                const response = await apiClient.post({
                    route: "post/user/follower",
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async delete(payload: UserFollowerPayload) {
            try {
                const response = await apiClient.delete({
                    route: `post/user/follower/${payload.user_id}?`,
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async check(payload: UserFollowerPayload) {
            try {
                const response = await apiClient.get({
                    route: `post/user/follower/check/${payload.user_id}`,
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async list(payload: ListUserFollowerPayload) {
            try {
                const parameters = apiClient.toURLSearchParams(payload);
                const response = await apiClient.get({
                    route: `post/user/follower?${parameters}`,
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },
    },
});
