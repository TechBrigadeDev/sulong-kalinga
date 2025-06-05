import AvatarImage from "components/Avatar";
import { Stack, useRouter } from "expo-router";
import { SafeAreaView, TouchableOpacity } from "react-native";
import { Avatar,ScrollView, Text, XStack} from "tamagui";

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
            <TouchableOpacity
                key={user.id}
                onPress={() => router.push(`/messaging/${user.id}`)}
            >
                <XStack
                //   ai="center"
                //   p="$4"
                //   gap="$3"
                //   mb="$2"
                //   bc="$background"
                //   bw={1}
                //   bColor="$gray5"
                //   br="$4"
                >
                <Avatar circular size="$10">
                  <AvatarImage
                    uri={user.avatar}
                    fallback={user.id.toString()}
                  />
                </Avatar>
                <Text fontSize={16}>{user.name}</Text>
                </XStack>
            </TouchableOpacity>
            ))}
        </ScrollView>
    </SafeAreaView>
  );
};

export default Screen;