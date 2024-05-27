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
            try {
                await apiClient.get({
                    route: `auth/api/check`,
                });
            } catch (e) {
                this.disableAuth();
                this.removeAuth();
            }
        },
    },
});
