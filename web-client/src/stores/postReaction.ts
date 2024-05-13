import { defineStore } from "pinia";
import apiClient from "@/lib/api-client";

export interface PostReactionPayload {
    name?: string;
    post_id?: number;
}

export const usePostReactionStore = defineStore("post-reaction", {
    actions: {
        async create(payload: PostReactionPayload) {
            try {
                const response = await apiClient.post({
                    route: "post/api/reaction",
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async delete(payload: PostReactionPayload) {
            try {
                const response = await apiClient.delete({
                    route: `post/api/reaction/${payload.post_id}?name=${payload.name}`,
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async check(payload: PostReactionPayload) {
            try {
                const response = await apiClient.get({
                    route: `post/api/reaction/check/${payload.post_id}?name=${payload.name}`,
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },
    },
});
