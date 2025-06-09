import AvatarImage from "components/Avatar";
import {
    StyleSheet,
    TouchableOpacity,
} from "react-native";
import {
    Avatar,
    Text,
    XStack,
    YStack,
} from "tamagui";

import { ChatListItemProps } from "~/features/messaging/type";

export const ChatListItem = ({
    chat,
    onPress,
}: ChatListItemProps) => (
    <TouchableOpacity onPress={onPress}>
        <XStack style={styles.chatItem}>
            <Avatar circular size="$6">
                <AvatarImage
                    uri={chat.avatar}
                    fallback={chat.id}
                />
            </Avatar>
            <YStack style={styles.chatContent}>
                <XStack style={styles.chatHeader}>
                    <Text style={styles.chatName}>
                        {chat.name}
                    </Text>
                    <Text style={styles.chatTime}>
                        {chat.time}
                    </Text>
                </XStack>
                <XStack style={styles.chatFooter}>
                    <Text
                        style={styles.chatMessage}
                        numberOfLines={1}
                    >
                        {chat.lastMessage}
                    </Text>
                    {chat.hasUnread && (
                        <YStack
                            style={
                                styles.unreadBadge
                            }
                        >
                            <Text
                                style={
                                    styles.unreadText
                                }
                            >
                                •
                            </Text>
                        </YStack>
                    )}
                </XStack>
            </YStack>
        </XStack>
    </TouchableOpacity>
);

const styles = StyleSheet.create({
    chatItem: {
        paddingVertical: 12,
        paddingHorizontal: 16,
        flexDirection: "row",
        alignItems: "center",
        gap: 12,
    },
    chatContent: {
        flex: 1,
        gap: 4,
    },
    chatHeader: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
    },
    chatFooter: {
        flexDirection: "row",
        justifyContent: "space-between",
        alignItems: "center",
    },
    chatName: {
        fontSize: 16,
        fontWeight: "600",
    },
    chatTime: {
        fontSize: 12,
        color: "#666",
    },
    chatMessage: {
        fontSize: 14,
        color: "#666",
        flex: 1,
        marginRight: 8,
    },
    unreadBadge: {
        width: 16,
        height: 16,
        borderRadius: 8,
        backgroundColor: "#ff6b00",
        alignItems: "center",
        justifyContent: "center",
    },
    unreadText: {
        color: "#fff",
        fontSize: 12,
    },
});
