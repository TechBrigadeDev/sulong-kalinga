import { SafeAreaView, StyleSheet } from "react-native";
import { Button, Card } from "tamagui";
import { Stack, useRouter } from "expo-router";
import BeneficiariesSearch from "~/features/user/management/components/beneficiaries/list/seach";
import BeneficiaryList from "~/features/user/management/components/beneficiaries/list";

const Beneficiaries = () => {
    const router = useRouter();

    const handleAddBeneficiary = () => {
        router.push("/user-management/beneficiaries/add");
    }

    return (
            <SafeAreaView style={styles.container}>
                <Stack.Screen options={{ 
                    title: 'Beneficiaries',
                 }} />
                <Card
                    paddingVertical={20}
                    marginVertical={20}
                    borderRadius={10}
                    display="flex"
                    gap="$4"
                    >
                    <Button
                        size="$3"
                        theme={"dark_blue"}
                        onPressIn={handleAddBeneficiary}
                    >
                            Add Beneficiary
                    </Button>
                    <BeneficiariesSearch/>
                </Card>
                <BeneficiaryList/>
            </SafeAreaView>
    )
}

const styles = StyleSheet.create({
    container: {
        marginHorizontal: 20,
    }
})



export default Beneficiaries;