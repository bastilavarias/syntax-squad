import { defineStore } from "pinia";
import apiClient from "@/lib/api-client";
import { ListPostCommentsPayload } from "@/stores/post.ts";

export interface CreatePostCommentPayload {
    post_id?: number;
    content?: string;
}
export interface ListPostCommentsPayload {
    post_id?: number;
    user_id?: number;
    page?: number;
    per_page?: number;
    sort_by?: string;
    is_draft?: number;
}

export const usePostCommentStore = defineStore("post-comment", {
    actions: {
        async create(payload: CreatePostCommentPayload) {
            try {
                const response = await apiClient.post({
                    route: "post/comment",
                    body: payload,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async list(payload: ListPostCommentsPayload) {
            try {
                const parameters = apiClient.toURLSearchParams(payload);
                const response = await apiClient.get({
                    route: `post/comment?${parameters}`,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },
    },
});
