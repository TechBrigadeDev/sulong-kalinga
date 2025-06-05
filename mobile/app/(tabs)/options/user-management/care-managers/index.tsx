import { Stack, useRouter } from "expo-router";
import { StyleSheet } from "react-native";
import { Button, View, YStack } from "tamagui"

import CareManagerList from "~/features/user/management/components/care-managers/list";
import CareManagerSearch from "~/features/user/management/components/care-managers/list/search";

const CareManagers = () => {
    const router = useRouter();

    const handleAddCareManager = () => {
        router.push("/(tabs)/options/user-management/care-managers/add");
    };

    return (
      <View flex={1} bg="#E1BEE7">
        <Stack.Screen
          options={{
            title: "Care Managers",
          }}
        />
        <View style={style.container}>
          <YStack py="$4" gap="$4">
            <Button
              size="$3"
              theme="dark_blue"
              onPressIn={handleAddCareManager}
            >
              Add Care Manager
            </Button>
            <CareManagerSearch/>
          </YStack>
          <View style={{ flex: 1 }}>
            <CareManagerList />
          </View>
        </View>
      </View>
    )
}

const style = StyleSheet.create({
  container: {
    flex: 1,
    paddingHorizontal: 16,
  },
});

export default CareManagers;