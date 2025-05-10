import { Stack } from "expo-router";
import { SafeAreaView } from "react-native";
import { Text, View } from "tamagui";

const BeneficiaryAdd = () => {

    return (
        <SafeAreaView>
            <Stack.Screen options={{
                headerShown: true,
                title: "Add Beneficiary",
            }}/>
            <View>
                <Text>Add Beneficiary</Text>
                <Text>Form to add a new beneficiary will go here</Text>
            </View>
        </SafeAreaView>
    )
}

export default BeneficiaryAdd;