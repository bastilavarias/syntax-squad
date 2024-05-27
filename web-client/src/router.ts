import { createRouter, createWebHistory } from "vue-router";
import { toast } from "@/components/ui/toast";
import { useAuthStore } from "@/stores/auth.ts";

const routes = [
    {
        path: "/",
        name: "home-page",
        component: () => import("@/pages/HomePage.vue"),
    },
    {
        path: "/form/:operation",
        name: "post-form-page",
        component: () => import("@/pages/PostFormPage.vue"),
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/chat",
        component: () => import("@/pages/ChatPage.vue"),
        children: [
            {
                path: "",
                name: "chat-page",
                component: () => import("@/pages/ChatRoomPage.vue"),
            },
            {
                path: "room/:roomID",
                name: "chat-conversation-page",
                component: () => import("@/pages/ChatConversationPage.vue"),
            },
        ],
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/search",
        name: "search-page",
        component: () => import("@/pages/SearchPage.vue"),
    },
    {
        path: "/profile/:username",
        name: "profile-page",
        component: () => import("@/pages/ProfilePage.vue"),
    },
    {
        path: "/0auth/:provider",
        name: "auth-callback-page",
        component: () => import("@/pages/AuthCallbackPage.vue"),
    },
    {
        path: "/:username/:slug",
        name: "view-post-page",
        component: () => import("@/pages/PostPage.vue"),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0 };
        }
    },
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    const isProtectedRoute = to.matched.some(
        (record) => record.meta.requiresAuth
    );
    if (isProtectedRoute) {
        await authStore.refresh();
    }
    const isAuthenticated = authStore.isAuthenticated;
    if (isProtectedRoute && !isAuthenticated) {
        toast({
            title: "Session expired.",
            description:
                "Your session expired. Please login again to use SyntaxSquad. Thankyou.",
        });
        return next({ name: "home-page" });
    }
    next();
});

export default router;
