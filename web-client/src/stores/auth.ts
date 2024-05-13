import { defineStore } from "pinia";
import apiClient from "@/lib/api-client";
import { LocationQueryValue } from "vue-router";

export interface AuthLoginPayload {
    code?: string | null;
}

export const useAuthStore = defineStore("auth", {
    state: () => ({
        isAuthenticated: false,
        token: null,
        user: null,
    }),

    actions: {
        establishAuth({ user, token }) {
            this.user = Object.assign({}, user);
            this.token = token;
            this.isAuthenticated = true;
            window.localStorage.setItem("token", token);
            window.localStorage.setItem("user", JSON.stringify(user));
        },
        disableAuth() {
            this.isAuthenticated = false;
        },
        removeAuth() {
            window.localStorage.removeItem("token");
            window.localStorage.removeItem("user");
            this.token = null;
            this.user = null;
        },

        async login(payload: AuthLoginPayload) {
            try {
                const response = await apiClient.post({
                    route: "auth/api/login",
                    body: payload,
                });
                const authData = response.data;
                this.establishAuth({
                    user: authData.user,
                    token: authData.token,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },
        async refresh() {
            /*  try {
                const response = await apiClient.get({
                    route: `auth/refresh`,
                });
                const authData = response.data;
                this.establishAuth({
                    user: authData.user,
                    token: authData.token,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                this.disableAuth();
                this.removeAuth();
                return await apiClient.toReadableResponse("error", e);
            }*/
            const user = window.localStorage.getItem("user");
            const token = window.localStorage.getItem("token");
            if (user && token) {
                this.establishAuth({
                    user: JSON.parse(user),
                    token,
                });
            }
        },

        async logout() {
            try {
                const response = await apiClient.get({
                    route: `auth/logout`,
                });
                return await apiClient.toReadableResponse("complete", response);
            } catch (e) {
                return await apiClient.toReadableResponse("error", e);
            }
        },
    },
});
