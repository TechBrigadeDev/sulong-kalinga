import { Stack, useRouter } from "expo-router";
import { StyleSheet } from "react-native";
import { Button, Card, View } from "tamagui"
import CareWorkerSearch from "~/features/user/management/components/care-workers/list/search";
import CareWorkerList from "~/features/user/management/components/care-workers/list";

const CareWorkers = () => {
    const router = useRouter();

    const handleAddCareWorker = () => {
        router.push(`/(tabs)/options/user-management/care-workers/add`);
    };
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
            <Button
              size="$3"
              theme="dark_blue"
              onPressIn={handleAddCareWorker}
            >
              Add Care Worker 
            </Button>
            <CareWorkerSearch/>
          </Card>
          <CareWorkerList />
        </View>
      </View>
    );
}

const style = StyleSheet.create({
    container: {
        marginHorizontal: 30
    }
})
export default CareWorkers;