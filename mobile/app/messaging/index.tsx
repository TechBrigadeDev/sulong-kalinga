import { Pressable, SafeAreaView } from "react-native";
import { ScrollView, XStack, Text, Avatar} from "tamagui";
import { Stack, useRouter } from "expo-router";

const Screen = () => {
  const router = useRouter();
  const users = [
    { id: "1", name: "Alice", avatar: "https://placekitten.com/200/200" },
    { id: "2", name: "Bob", avatar: "https://placekitten.com/201/201" },
    { id: "3", name: "Charlie", avatar: "https://placekitten.com/202/202" },
    { id: "4", name: "Diana", avatar: "https://placekitten.com/203/203" },
    { id: "5", name: "Eve", avatar: "https://placekitten.com/204/204" },
  ];
  return (
    <SafeAreaView>
        <Stack.Screen
            options={{
                title: "Messaging",
                headerShown: true,
            }}
        />
        <ScrollView contentContainerStyle={{ padding: 10 }}>
            {users.map((user) => (
            <Pressable
                key={user.id}
                onPress={() => router.push(`/messaging/${user.id}`)}
            >
                <XStack
                //   ai="center"
                //   p="$4"
                //   space="$3"
                //   mb="$2"
                //   bc="$background"
                //   bw={1}
                //   bColor="$gray5"
                //   br="$4"
                >
                <Avatar />
                <Text fontSize={16}>{user.name}</Text>
                </XStack>
            </Pressable>
            ))}
        </ScrollView>
    </SafeAreaView>
  );
};

export default Screen;