import { useGetCareManagers, useGetCareWorkers } from "~/features/user/management/management.hook";
import { FlatList, ListRenderItem, RefreshControl, SafeAreaView } from "react-native";
import { Button, Card, Text, View } from "tamagui";
import { useRouter } from "expo-router";
import { careManagerListStore } from "./store";
import { ICareManager } from "~/features/user/management/management.type";


const CareManagerList = () => {
    const {
        search
    } = careManagerListStore();

    const {
        data = [],
        isLoading,
        refetch
    } = useGetCareManagers({
        search
    });


    if (data.length === 0 && !isLoading) {
      return <Text>No Care Manager found</Text>;
    }

    return (
      <FlatList
        data={data}
        renderItem={CareManagerCard}
        contentContainerStyle={{ padding: 8 }}
        refreshControl={
          <RefreshControl refreshing={isLoading} onRefresh={refetch} />
        }
      />
    );
}


const CareManagerCard: ListRenderItem<ICareManager> = ({ item }) => {
  const router = useRouter();

  const { id, first_name, last_name } =
    item;

  const onView = () => {
    router.push(`/(tabs)/options/user-management/care-managers/${id}`);
  };

  return (
    <Card
      theme="light_white"
      marginBottom="$2"
      marginHorizontal="$2"
      elevate
      bordered
      padding="$3"
    >
      <View>
        <Text fontSize="$6" fontWeight="500">
          {first_name} {last_name}
        </Text>
      </View>
      <View style={{ flexDirection: "row", gap: 8, marginTop: 12 }}>
        <Button
          size="$3"
          theme="light"
          borderColor="gray"
          onPress={onView}
          variant="outlined"
        >
          View
        </Button>
      </View>
    </Card>
  );
};

export default CareManagerList;