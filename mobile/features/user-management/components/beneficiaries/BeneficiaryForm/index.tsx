import ScrollView from "components/ScrollView";
import { Stack } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { Button, YStack } from "tamagui";
import { zodResolver } from "@hookform/resolvers/zod";
import { FormProvider, useForm } from "react-hook-form";

import { IBeneficiary } from "~/features/user-management/management.type";

import { PersonalDetailsSection } from "./components/PersonalDetailsSection";
import {
    beneficiaryFormSchema,
    beneficiaryFormDefaults,
    BeneficiaryFormValues,
} from "./schema";

interface Props {
    beneficiary?: IBeneficiary;
    onSubmit?: (data: BeneficiaryFormValues) => Promise<void>;
}

const BeneficiaryForm = ({ beneficiary, onSubmit }: Props) => {
    const form = useForm({
        resolver: zodResolver(beneficiaryFormSchema),
        defaultValues: beneficiaryFormDefaults,
    });

    const handleSubmit = async (data: any) => {
        try {
            console.log("Submitting beneficiary data:", data);
            if (onSubmit) {
                await onSubmit(data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
        }
    };

    return (
        <FormProvider {...form}>
            <SafeAreaView style={{ flex: 1 }}>
                <Stack.Screen
                    options={{
                        title: beneficiary ? "EDIT BENEFICIARY" : "ADD BENEFICIARY",
                        headerShown: true,
                    }}
                />
                <ScrollView>
                    <YStack gap="$4" p="$4">
                        <PersonalDetailsSection />
                        
                        {/* TODO: Uncomment as you convert each section to React Hook Form */}
                        {/* <AddressSection /> */}
                        
                        <Button
                            onPress={form.handleSubmit(handleSubmit)}
                            disabled={form.formState.isSubmitting}
                            size="$4"
                            mt="$4"
                            theme="blue"
                        >
                            {form.formState.isSubmitting ? "Submitting..." : "Submit"}
                        </Button>

                        {/* TODO: Add other sections with React Hook Form Controllers */}
                        {/* 
                        <MedicalHistorySection />
                        <CareNeedsSection />
                        <MedicationSection />
                        <CognitiveFunctionSection />
                        <EmergencyContactSection />
                        <DocumentsSection /> 
                        */}
                    </YStack>
                </ScrollView>
            </SafeAreaView>
        </FormProvider>
    );
};

export default BeneficiaryForm;
