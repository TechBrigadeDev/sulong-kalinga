import { Stack } from "expo-router";
import { Card, View } from "tamagui"
import { StyleSheet } from "react-native";
import CareManagerSearch from "~/features/user/management/components/care-managers/list/search";
import CareManagerList from "~/features/user/management/components/care-managers/list";

const CareManagers = () => {
    return (
      <View flex={1} bg="$background">
        <Stack.Screen
          options={{
            title: "Care Workers",
          }}
        />
        <View style={style.container}>
          <Card
            paddingVertical={20}
            marginVertical={20}
            borderRadius={10}
            display="flex"
            gap="$4"
          >
            <CareManagerSearch/>
          </Card>
          <CareManagerList />
        </View>
      </View>
    )
}

const style = StyleSheet.create({
  container: {
    marginHorizontal: 30,
  },
});

export default CareManagers;