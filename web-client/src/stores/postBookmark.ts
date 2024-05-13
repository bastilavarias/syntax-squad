import { defineStore } from "pinia";
import apiClient from "@/lib/api-client";

export interface PostBookmarkPayload {
    post_id?: number;
}
export interface ListBookmarksPayload {
    page?: number;
    per_page?: number;
    user_id?: number;
}

export const usePostBookmarkStore = defineStore("post-bookmark", {
    actions: {
        async create(payload: PostBookmarkPayload) {
            try {
                const response = await apiClient.post({
                    route: "post/api/bookmark",
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async delete(payload: PostBookmarkPayload) {
            try {
                const response = await apiClient.delete({
                    route: `post/api/bookmark/${payload.post_id}?`,
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async check(payload: PostBookmarkPayload) {
            try {
                const response = await apiClient.get({
                    route: `post/api/bookmark/check/${payload.post_id}`,
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async list(payload: ListBookmarksPayload) {
            try {
                const parameters = apiClient.toURLSearchParams(payload);
                const response = await apiClient.get({
                    route: `post/api/bookmark?${parameters}`,
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },
    },
});
