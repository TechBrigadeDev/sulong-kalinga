import { useRouter } from "expo-router";
import { useGetBeneficiaries } from "features/user/management/management.hook";
import {
  FlatList,
  ListRenderItem,
  RefreshControl,
  StyleSheet,
} from "react-native";
import { Button, Card, Text, View } from "tamagui";

import { IBeneficiary } from "~/features/user/management/management.type";

import { beneficiaryListStore } from "./store";

const BeneficiaryList = () => {
  const { search } = beneficiaryListStore();

  const {
    data = [],
    isLoading,
    refetch,
  } = useGetBeneficiaries({
    search,
  });

  if (data.length === 0 && !isLoading) {
    return (
      <View>
        <Text>No beneficiaries found</Text>
      </View>
    );
  }

  return (
    <FlatList
      data={data}
      renderItem={BeneficiaryCard}
      contentContainerStyle={listStyle.container}
      refreshControl={
        <RefreshControl refreshing={isLoading} onRefresh={refetch} />
      }
    />
  );
};

const listStyle = StyleSheet.create({
  container: {
    paddingHorizontal: 16,
    paddingBottom: 50
  },
});

const BeneficiaryCard: ListRenderItem<IBeneficiary> = ({ item }) => {
  const router = useRouter();

  const { beneficiary_id, first_name, last_name } = item;

  const onView = () => {
    router.push(`/(tabs)/options/user-management/beneficiaries/${beneficiary_id}`);
  };

  const onEdit = () => {
    router.push(`/(tabs)/options/user-management/beneficiaries/${beneficiary_id}/edit`);
  };

  return (
    <Card
      theme="light"
      marginBottom="$2"
      bordered
      padding="$3"
    >
      <Text fontSize="$6" fontWeight="500">
        {first_name} {last_name}
      </Text>
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

export default BeneficiaryList;
