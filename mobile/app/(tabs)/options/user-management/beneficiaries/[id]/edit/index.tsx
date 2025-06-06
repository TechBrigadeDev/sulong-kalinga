import LoadingScreen from "components/loaders/LoadingScreen";
import { Redirect, Stack, useLocalSearchParams } from "expo-router";
import { Text, View } from "tamagui";

import BeneficiaryForm from "~/features/user-management/components/beneficiaries/BeneficiaryForm";
import { useGetBeneficiary } from "~/features/user-management/management.hook";

const Screen = () => {
    const { id } = useLocalSearchParams();

    const { data, isLoading, error } = useGetBeneficiary(id as string);

    if (isLoading) {
        return <LoadingScreen />;
    }

    if (!isLoading && error) {
        console.error("Error fetching beneficiary:", error);
        return <Redirect href="/(tabs)/options/user-management/beneficiaries" />;
    }

    if (!data) {
        return (
            <View>
                <Text>No beneficiary found</Text>
            </View>
        );
    }

    return (
        <>
            <Stack.Screen
                options={{
                    title: "Edit Beneficiary",
                }}
            />
            <BeneficiaryForm beneficiary={data} />
        </>
    );
};

export default Screen;
