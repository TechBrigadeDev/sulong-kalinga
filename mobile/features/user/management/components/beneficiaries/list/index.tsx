import { FlatList, ListRenderItem, RefreshControl, StyleSheet, TouchableNativeFeedback } from "react-native";
import { IBeneficiary } from "../../../../user.schema";
import { useGetBeneficiaries } from "../../../management.hook";
import { Card, Text, View } from "tamagui";
import { beneficiaryListStore } from "./store";
import { useRoute } from "@react-navigation/native";
import { useRouter } from "expo-router";


const BeneficiaryList = () => {
    const {
        search,
    } = beneficiaryListStore();

    const {
        data = [],
        isLoading,
        refetch
    } = useGetBeneficiaries({
        search
    });

    if (data.length === 0 && !isLoading) {
        return (
            <View>
                <Text>No beneficiaries found</Text>
            </View>
        )
    }

    return (
        <FlatList 
            data={data}
            renderItem={BeneficiaryCard}
            refreshControl={
                <RefreshControl
                    refreshing={isLoading}
                    onRefresh={refetch}
                />
            }
        />
    )
}

const styles = StyleSheet.create({
    container: {
        paddingHorizontal: 20,
    },
    menuContainer: {
        display: "flex",
        justifyContent: "center",
        alignItems: "center",
        marginVertical: 20,
        paddingVertical: 25
    }
})

const BeneficiaryCard: ListRenderItem<IBeneficiary> = ({
    item
}) => { 
  const router = useRouter();

  const {
    beneficiary_id,
    first_name,
    last_name
  } = item;
  
  const onPress = () => {
    router.push(`/user-management/beneficiaries/${beneficiary_id}`);
  }

  return (
    <TouchableNativeFeedback onPressIn={onPress}>
      <Card 
        theme="light_white" 
        marginBottom="$4"
        backgroundColor={"white"}
        elevate>
        <Card.Header>
            <Text>{first_name} {last_name}</Text>
        </Card.Header>
      </Card>
    </TouchableNativeFeedback>
  );
};

export default BeneficiaryList;