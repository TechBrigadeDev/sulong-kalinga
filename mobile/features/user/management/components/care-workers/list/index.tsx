import { useRouter } from "expo-router";
import { careManagerListStore } from "features/user/management/components/care-managers/list/store";
import { ICareWorker } from "features/user/management/management.type";
import { RefreshControl } from "react-native";
import { Button, Card, Text, View } from "tamagui";

import FlatList from "~/components/FlatList";
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
      return <Text>No care workers found</Text>;
    }

    return (
      <FlatList
        data={data}
        renderItem={({ item }) => <CareWorkerCard item={item} />}
        contentContainerStyle={{ paddingBottom: 120 }}
        refreshControl={
          <RefreshControl refreshing={isLoading} onRefresh={refetch} />
        }
      />
    );
}


interface CareWorkerCardProps {
  item: ICareWorker;
}

const CareWorkerCard = ({ item }: CareWorkerCardProps) => {
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
      theme="light"
      marginBottom="$2"
      padding="$3"
      bg="#F8F9FA"
      borderRadius={8}
      borderColor="#E9ECEF"
      borderWidth={1}
    >
      <View>
        <Text fontSize="$6" fontWeight="500" color="#495057">
          {first_name} {last_name}
        </Text>
      </View>
      <View style={{ flexDirection: "row", gap: 8, marginTop: 12 }}>
        <Button
          size="$3"
          bg="#E9ECEF"
          color="#495057"
          borderColor="#DEE2E6"
          onPress={onView}
          variant="outlined"
        >
          View
        </Button>
        <Button
          size="$3"
          bg="#E9ECEF"
          color="#495057"
          borderColor="#DEE2E6"
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