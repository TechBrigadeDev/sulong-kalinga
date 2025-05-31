import { Stack, useRouter } from "expo-router";

import BeneficiaryForm from "~/features/user/management/components/beneficiaries/BeneficiaryForm";
import { IBeneficiary } from "~/features/user/management/management.type";

const BeneficiaryAdd = () => {
    const router = useRouter();

    const handleSubmit = (data: Partial<IBeneficiary>) => {
        console.log("Submitting beneficiary data:", data);
        // TODO: Add API call to create beneficiary
        router.back();
    };

    return (
        <>
            <Stack.Screen options={{
                headerShown: true,
                title: "Add Beneficiary",
            }}/>
            <BeneficiaryForm onSubmit={handleSubmit} />
        </>
    );
};

export default BeneficiaryAdd;