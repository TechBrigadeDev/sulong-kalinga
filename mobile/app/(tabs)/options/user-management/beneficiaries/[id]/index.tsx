import LoadingScreen from "components/loaders/LoadingScreen";
import {
    Redirect,
    Stack,
    useLocalSearchParams,
} from "expo-router";
import BeneficiaryDetail from "features/user-management/components/beneficiaries/detail";
import { useGetBeneficiary } from "features/user-management/management.hook";
import { Text, View } from "tamagui";

const Screen = () => {
    const { id } = useLocalSearchParams();

    const { data, isLoading, error } =
        useGetBeneficiary(id as string);

    if (isLoading) {
        return <LoadingScreen />;
    }

    if (!isLoading && error) {
        console.error(
            "Error fetching beneficiary:",
            error,
        );
        return (
            <Redirect href="/(tabs)/options/user-management/beneficiaries" />
        );
    }

    if (!data) {
        return (
            <View>
                <Text>No beneficiary found</Text>
            </View>
        );
    }

    return (
        <BeneficiaryDetail beneficiary={data} />
    );
};

const Layout = () => (
    <>
        <Stack.Screen
            options={{
                headerTitle: "Beneficiary",
            }}
        />
        <Screen />
    </>
);

export default Layout;
