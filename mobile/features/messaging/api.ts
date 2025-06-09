import { Controller } from "common/api";

import { ChatItem } from "./type";

// TODO: Implement actual API calls
export const getChats = async (): Promise<
    ChatItem[]
> => {
    return [
        {
            id: "1",
            name: "John Doe",
            avatar: "https://example.com/avatar1.png",
            lastMessage: "Hello!",
            time: "10:00 AM",
            isPinned: false,
            hasUnread: true,
        },
        {
            id: "2",
            name: "Jane Smith",
            avatar: "https://example.com/avatar2.png",
            lastMessage: "How are you?",
            time: "10:05 AM",
            isPinned: true,
            hasUnread: false,
        },
        {
            id: "3",
            name: "Alice Johnson",
            avatar: "https://example.com/avatar3.png",
            lastMessage: "Let's catch up!",
            time: "10:10 AM",
            isPinned: false,
            hasUnread: true,
        },
    ];
};

export const getPinnedChats = async (): Promise<
    ChatItem[]
> => {
    return [];
};

class ChatController extends Controller {
    async getThreads() {
        const response = await this.api.get(
            "/messaging/thread",
        );
        const data = response.data;

        console.log(data, "\n\n\nChatController");
        return data;

        // const validate =
        //     await appController.chatListResponseSchema.safeParseAsync(
        //         data,
        //     );
        // if (!validate.success) {
        //     console.error(
        //         "Error validating chat list data",
        //         validate.error,
        //     );
        //     throw new Error(
        //         "Invalid data format received from the server",
        //     );
        // }

        // return validate.data;
    }
}

export const chatController =
    new ChatController();
