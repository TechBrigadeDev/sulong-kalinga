import { Stack, useRouter } from "expo-router";
import { StyleSheet } from "react-native";
import { Button, View, YStack } from "tamagui";

import BeneficiaryList from "~/features/user-management/components/beneficiaries/list";
import BeneficiariesSearch from "~/features/user-management/components/beneficiaries/list/seach";

const Beneficiaries = () => {
    const router = useRouter();

    const handleAddBeneficiary = () => {
        router.push("/(tabs)/options/user-management/beneficiaries/add");
    };

    return (
        <View flex={1} bg="#BBDEFB">
            <Stack.Screen
                options={{
                    title: "Beneficiaries",
                }}
            />
            <View style={styles.container}>
                <YStack py="$4" gap="$4">
                    <Button size="$3" theme="dark_blue" onPressIn={handleAddBeneficiary}>
                        Add Beneficiary
                    </Button>
                    <BeneficiariesSearch />
                </YStack>
                <View style={{ flex: 1 }}>
                    <BeneficiaryList />
                </View>
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        paddingHorizontal: 16,
    },
});

export default Beneficiaries;
