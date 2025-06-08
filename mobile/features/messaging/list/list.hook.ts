import { useQuery } from "@tanstack/react-query";
import { useState } from "react";

import {
    getChats,
    getPinnedChats,
} from "./list.api";

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
