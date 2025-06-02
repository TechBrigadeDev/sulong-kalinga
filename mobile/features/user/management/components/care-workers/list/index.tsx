import { FlashList, ListRenderItem } from "@shopify/flash-list";
import { useRouter } from "expo-router";
import { careManagerListStore } from "features/user/management/components/care-managers/list/store";
import { ICareWorker } from "features/user/management/management.type";
import { RefreshControl } from "react-native";
import { Button, Card, Text, View } from "tamagui";

import { useGetCareWorkers } from "~/features/user/management/management.hook";


const CareWorkerList = () => {
    const {
        search
    } = careManagerListStore();

    const {
        data = [],
        isLoading,
        refetch
    } = useGetCareWorkers({
        search
    });


    if (data.length === 0 && !isLoading) {
      return <Text>No family members found</Text>;
    }

    return (
      <FlashList
        data={data}
        renderItem={CareWorkerCard}
        contentContainerStyle={{ padding: 8 }}
        refreshControl={
          <RefreshControl refreshing={isLoading} onRefresh={refetch} />
        }
        estimatedItemSize={100}
        keyExtractor={(item) => item.id.toString()}
      />
    );
}


const CareWorkerCard: ListRenderItem<ICareWorker> = ({ item }) => {
  const router = useRouter();

  const { id, first_name, last_name } =
    item;

  const onView = () => {
    router.push(`/(tabs)/options/user-management/care-workers/${id}`);
  };

  const onEdit = () => {
    router.push(
      `/(tabs)/options/user-management/care-workers/${id}/edit`
    );
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
        <Button
          size="$3"
          theme="light"
          borderColor="gray"
          onPress={onEdit}
          variant="outlined"
        >
          Edit
        </Button>
      </View>
    </Card>
  );
};

export default CareWorkerList;