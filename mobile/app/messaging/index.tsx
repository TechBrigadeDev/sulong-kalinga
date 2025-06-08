import { Stack } from "expo-router";
import { ChatList } from "features/messaging/list";

const Screen = () => {
    return (
        <>
            <Stack.Screen
                options={{
                    title: "Chat",
                    headerShown: true,
                }}
            />
            <ChatList />
        </>
    );
};

export default Screen;
