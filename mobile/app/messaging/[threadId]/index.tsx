import { Stack, useLocalSearchParams } from "expo-router";
import { Text, View } from "tamagui";


const Screen = () => {
    const { threadId } = useLocalSearchParams();

    return (
        <View>
            <Stack.Screen options={{
                headerShown: true,
                title: `Messaging Thread ${threadId}`,
            }}/>
             
            <Text>
                Messaging Thread
            </Text>
            <Text>
                Thread ID: {threadId}
            </Text>
        </View>
    );
}

export default Screen;