import { useRouter } from "expo-router";
import { RefreshControl } from "react-native";
import { Button, Card, Text, View } from "tamagui";

import FlatList from "~/components/FlatList";
import { useGetCareManagers } from "~/features/user-management/management.hook";
import { ICareManager } from "~/features/user-management/management.type";

import { careManagerListStore } from "./store";

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
        renderItem={({ item }) => <CareManagerCard item={item} />}
        contentContainerStyle={{ paddingBottom: 120 }}
        refreshControl={
          <RefreshControl refreshing={isLoading} onRefresh={refetch} />
        }
      />
    );
}


interface CareManagerCardProps {
  item: ICareManager;
}

const CareManagerCard = ({ item }: CareManagerCardProps) => {
  const router = useRouter();

  const { id, first_name, last_name } =
    item;

  const onView = () => {
    router.push(`/(tabs)/options/user-management/care-managers/${id}`);
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
      </View>
    </Card>
  );
};

export default CareManagerList;