import { useQuery } from "@tanstack/react-query";
import { QK } from "common/query";
import { authStore } from "features/auth/auth.store";
import { useState } from "react";

import {
    chatController,
    getChats,
    getPinnedChats,
} from "./api";

const api = chatController;

export const useChatList = () => {
    const [searchQuery, setSearchQuery] =
        useState("");

    const { data: chats = [] } = useQuery({
        queryKey: ["chats"],
        queryFn: getChats,
    });

    const { data: pinnedChats = [] } = useQuery({
        queryKey: ["pinnedChats"],
        queryFn: getPinnedChats,
    });

    return {
        chats,
        pinnedChats,
        searchQuery,
        setSearchQuery,
    };
};

export const useChatThreads = () => {
    const { token } = authStore();

    return useQuery({
        queryKey: [
            QK.messaging.threads.getThreads,
        ],
        queryFn: async () => {
            const response =
                await api.getThreads();

            return [];
        },
        enabled: !!token,
    });
};
