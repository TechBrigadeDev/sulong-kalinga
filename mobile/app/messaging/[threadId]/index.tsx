import { Stack, useLocalSearchParams } from "expo-router";
import { useState } from "react";
import { ScrollView } from "react-native";
import { Button, Input, Text, XStack, YStack } from "tamagui";

const Screen = () => {
    const { threadId } = useLocalSearchParams();
    const [message, setMessage] = useState("");

    // Dummy messages for demonstration
    const messages = [
        { id: 1, text: "Hey there!", sender: "them", timestamp: "10:00 AM" },
        { id: 2, text: "Hi! How are you?", sender: "me", timestamp: "10:01 AM" },
        {
            id: 3,
            text: "I'm good, thanks! Just wanted to check in.",
            sender: "them",
            timestamp: "10:02 AM",
        },
        { id: 4, text: "That's great to hear!", sender: "me", timestamp: "10:03 AM" },
    ];

    const handleSend = () => {
        if (message.trim()) {
            // Here you would typically send the message
            console.log("Sending message:", message, "to thread:", threadId);
            setMessage("");
        }
    };

    return (
        <YStack style={{ flex: 1 }}>
            <Stack.Screen
                options={{
                    headerShown: true,
                    title: `Chat ${threadId}`,
                }}
            />

            <ScrollView style={{ flex: 1 }} contentContainerStyle={{ padding: 16 }}>
                {messages.map((msg) => (
                    <XStack
                        key={msg.id}
                        style={{
                            justifyContent: msg.sender === "me" ? "flex-end" : "flex-start",
                            marginBottom: 8,
                        }}
                    >
                        <YStack
                            style={{
                                backgroundColor: msg.sender === "me" ? "#0084ff" : "#e4e6eb",
                                borderRadius: 16,
                                padding: 12,
                                maxWidth: "80%",
                            }}
                        >
                            <Text
                                style={{
                                    color: msg.sender === "me" ? "white" : "black",
                                }}
                            >
                                {msg.text}
                            </Text>
                            <Text
                                style={{
                                    fontSize: 12,
                                    color: msg.sender === "me" ? "#ffffff99" : "#00000099",
                                }}
                            >
                                {msg.timestamp}
                            </Text>
                        </YStack>
                    </XStack>
                ))}
            </ScrollView>

            <XStack
                style={{
                    padding: 16,
                    borderTopWidth: 1,
                    borderColor: "#e4e6eb",
                    alignItems: "center",
                    gap: 8,
                }}
            >
                <Input
                    style={{ flex: 1 }}
                    placeholder="Type a message..."
                    value={message}
                    onChangeText={setMessage}
                />
                <Button style={{ backgroundColor: "#0084ff" }} onPress={handleSend}>
                    <Text style={{ color: "white" }}>Send</Text>
                </Button>
            </XStack>
        </YStack>
    );
};

export default Screen;
