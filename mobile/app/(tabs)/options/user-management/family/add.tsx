import { Stack, useRouter } from "expo-router";

import FamilyMemberForm from "~/features/user-management/components/family/FamilyMemberForm";

const FamilyMemberAdd = () => {
    const router = useRouter();

    const handleSubmit = (data: any) => {
        console.log("Submitting family member data:", data);
        // TODO: Add API call to create family member
        router.back();
    };

    return (
        <>
            <Stack.Screen
                options={{
                    headerShown: true,
                    title: "Add Family Member",
                }}
            />
            <FamilyMemberForm onSubmit={handleSubmit} />
        </>
    );
};

export default FamilyMemberAdd;
