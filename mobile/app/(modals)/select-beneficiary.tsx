import SelectBeneficiary from "components/screens/Modals/SelectBeneficiary";
import { Stack } from "expo-router";

const Screen = () => {
    return (
        <>
            <Stack.Screen
                name="Select Beneficiary"
                options={{
                    presentation: 'modal',
                    headerShown: false,
                    title: 'Select Beneficiary',
                    headerTitle: 'Select Beneficiary',
                }}
            />
            <SelectBeneficiary/>
        </>
    )
}

export default Screen;