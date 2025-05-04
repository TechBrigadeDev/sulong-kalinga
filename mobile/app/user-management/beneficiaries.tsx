import { Text } from "@tamagui/core";
import { Card, ScrollView, View } from "tamagui";
import { useGetBeneficiaries } from "../../features/user/management/management.hook";
import { RefreshControl } from "react-native";

const Beneficiaries = () => {
    const {
        refetch,
        isLoading
    } = useGetBeneficiaries();


    const handleRefresh = () => {
        refetch();
    }
    return (
        <ScrollView
            refreshControl={
                <RefreshControl
                    refreshing={isLoading}
                    onRefresh={handleRefresh}
                />
            }
        >
            <List/>
        </ScrollView>
    )
}

const List = () => {
    const {
        data = [],
        isLoading
    } = useGetBeneficiaries();

    if (isLoading) {
        return null;
    }

    if (data.length === 0) {
        return (
            <View>
                <Text>No beneficiaries found</Text>
            </View>
        )
    }

    return (
        <View>
            <Text>{JSON.stringify(data)}</Text>
        </View>
    )
}

export default Beneficiaries;