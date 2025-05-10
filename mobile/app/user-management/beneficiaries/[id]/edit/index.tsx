import { Stack, useLocalSearchParams } from "expo-router";
import { Text, View } from "tamagui";
import { useGetBeneficiary } from "../../../../../features/user/management/management.hook";
import BeneficiaryForm from "../../../../../features/user/management/components/beneficiaries/BeneficiaryForm";


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
    return (
        <>
            <Stack.Screen
                options={{
                    title: 'Edit Beneficiary',
                }}
            />
            <BeneficiaryForm beneficiary={data} />
        </>
    )
}

export default Screen;