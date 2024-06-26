import { defineStore } from "pinia";
import apiClient from "@/lib/api-client";
import { LocationQueryValue } from "vue-router";

export interface CreatePostPayload {
    title?: string;
    content?: string;
    tags?: string[];
    is_draft?: boolean;
    cover_image?: File;
}
export interface ListPostsPayload {
    user_id?: number;
    page?: number;
    per_page?: number;
    search?: string;
    sort_by?: string;
    is_draft?: number;
}
export interface UpdatePostPayload {
    post_id?: number;
    title?: string;
    content?: string;
    tags?: string[];
    is_draft?: boolean;
    cover_image?: File;
    _method?: string;
}

export const usePostStore = defineStore("post", {
    state: () => ({
        content: {
            value: [],
            page: 1,
            perPage: 5,
        },
    }),

    actions: {
        async create(payload: CreatePostPayload) {
            try {
                const response = await apiClient.post({
                    route: "post",
                    body: payload,
                    transform: "form-data",
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async list(payload: ListPostsPayload) {
            try {
                const parameters = apiClient.toURLSearchParams(payload);
                const response = await apiClient.get({
                    route: `post?${parameters}`,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async get(postID: number) {
            try {
                const response = await apiClient.get({
                    route: `post/${postID}`,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async getBySlug(slug: string) {
            try {
                const response = await apiClient.get({
                    route: `post/public/${slug}`,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async update(payload: UpdatePostPayload) {
            try {
                const response = await apiClient.post({
                    route: `post/${payload.post_id}`,
                    body: payload,
                    transform: "form-data",
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },

        async delete(postID: number) {
            try {
                const response = await apiClient.delete({
                    route: `post/${postID}`,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },
    },
});
