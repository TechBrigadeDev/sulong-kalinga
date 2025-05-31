import { Stack, useLocalSearchParams } from "expo-router";
import { Text, View } from "tamagui";

import BeneficiaryDetail from "~/features/user/management/components/beneficiaries/detail";
import { useGetBeneficiary } from "~/features/user/management/management.hook";

const Screen = () => {
    const { id } = useLocalSearchParams();

    const {
     data,
     isLoading
    } = useGetBeneficiary(id as string);

    if (isLoading) {
        return (
            <View>
                <Text>Loading...</Text>
            </View>
        )
    }

    if (!data) {
        return (
            <View>
                <Text>No beneficiary found</Text>
            </View>
        )
    }

    return (
        <>
            <Stack.Screen/>
            <BeneficiaryDetail beneficiary={data}/>
        </>
    )
}

export default Screen;