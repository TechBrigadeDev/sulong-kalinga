export interface ChatItem {
    id: string;
    name: string;
    avatar: string;
    lastMessage: string;
    time: string;
    isPinned?: boolean;
    hasUnread?: boolean;
}

export interface ChatListItemProps {
    chat: ChatItem;
    onPress: () => void;
}
