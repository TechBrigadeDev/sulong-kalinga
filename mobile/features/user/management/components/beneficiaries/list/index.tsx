import {
  FlatList,
  ListRenderItem,
  RefreshControl,
  StyleSheet,
  TouchableNativeFeedback,
} from "react-native";
import { IBeneficiary } from "~/user.schema";
import { useGetBeneficiaries } from "../../../management.hook";
import { Button, Card, Text, View } from "tamagui";
import { beneficiaryListStore } from "./store";
import { useRouter } from "expo-router";

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
      contentContainerStyle={{ paddingVertical: 16 }}
      refreshControl={
        <RefreshControl refreshing={isLoading} onRefresh={refetch} />
      }
    />
  );
};

const BeneficiaryCard: ListRenderItem<IBeneficiary> = ({ item }) => {
  const router = useRouter();

  const { beneficiary_id, first_name, last_name } = item;

  const onView = () => {
    router.push(`/user-management/beneficiaries/${beneficiary_id}`);
  };

  const onEdit = () => {
    router.push(`/user-management/beneficiaries/${beneficiary_id}/edit`);
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
