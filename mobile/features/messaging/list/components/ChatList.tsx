import { Plus } from "@tamagui/lucide-icons";
import { useRouter } from "expo-router";
import { StyleSheet } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import {
    Button,
    ScrollView,
    Text,
    YStack,
} from "tamagui";

import { ChatListItem } from "./components/ChatListItem";
import { SearchBar } from "./components/SearchBar";
import { useChatList } from "./list.hook";

export const ChatList = () => {
    const router = useRouter();
    const {
        chats,
        pinnedChats,
        searchQuery,
        setSearchQuery,
    } = useChatList();

    return (
        <SafeAreaView style={styles.container}>
            <YStack style={styles.container}>
                <SearchBar
                    value={searchQuery}
                    onChangeText={setSearchQuery}
                />

                <ScrollView
                    style={styles.container}
                >
                    <YStack space>
                        {pinnedChats.length >
                            0 && (
                            <>
                                <Text
                                    style={
                                        styles.sectionHeader
                                    }
                                >
                                    PINNED
                                </Text>
                                {pinnedChats.map(
                                    (chat) => (
                                        <ChatListItem
                                            key={
                                                chat.id
                                            }
                                            chat={
                                                chat
                                            }
                                            onPress={() =>
                                                router.push(
                                                    `/messaging/${chat.id}`,
                                                )
                                            }
                                        />
                                    ),
                                )}
                            </>
                        )}

                        <Text
                            style={
                                styles.sectionHeader
                            }
                        >
                            ALL
                        </Text>
                        {chats.map((chat) => (
                            <ChatListItem
                                key={chat.id}
                                chat={chat}
                                onPress={() =>
                                    router.push(
                                        `/messaging/${chat.id}`,
                                    )
                                }
                            />
                        ))}
                    </YStack>
                </ScrollView>

                {/* New Chat Button */}
                <YStack
                    style={styles.fabContainer}
                >
                    <Button
                        size="$5"
                        circular
                        style={styles.fabButton}
                    >
                        <Plus
                            size={24}
                            color="#fff"
                        />
                    </Button>
                </YStack>
            </YStack>
        </SafeAreaView>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: "#fff",
    },
    sectionHeader: {
        fontSize: 14,
        color: "#666",
        paddingHorizontal: 16,
        paddingVertical: 8,
        fontWeight: "600",
    },
    fabContainer: {
        position: "absolute",
        bottom: 20,
        right: 20,
    },
    fabButton: {
        backgroundColor: "#ff6b00",
    },
});
