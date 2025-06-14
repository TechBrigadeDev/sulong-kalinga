import { zodResolver } from "@hookform/resolvers/zod";
import ScrollView from "components/ScrollView";
import { Stack } from "expo-router";
import {
    FormProvider,
    useForm,
} from "react-hook-form";
import { Alert } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { Button, YStack } from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

import { AddressSection } from "./components/AddressSection";
import { CareNeedsSection } from "./components/CareNeedsSection";
import { CognitiveFunctionSection } from "./components/CognitiveFunctionSection";
import { DocumentsSection } from "./components/Documents";
import { EmergencyContactSection } from "./components/EmergencyContactSection";
import { MedicalHistorySection } from "./components/MedicalHistorySection";
import { MedicationSection } from "./components/MedicationSection";
import { PersonalDetailsSection } from "./components/PersonalDetailsSection";
import {
    beneficiaryFormDefaults,
    beneficiaryFormSchema,
    BeneficiaryFormValues,
} from "./schema";

interface Props {
    beneficiary?: IBeneficiary;
    onSubmit?: (
        data: BeneficiaryFormValues,
    ) => Promise<void>;
}

const BeneficiaryForm = ({
    beneficiary,
    onSubmit,
}: Props) => {
    const form = useForm({
        resolver: zodResolver(
            beneficiaryFormSchema,
        ),
        defaultValues: beneficiaryFormDefaults,
        mode: "onChange",
    });

    const handleSubmit = async (data: any) => {
        try {
            console.log(
                "Submitting beneficiary data:",
                data,
            );
            if (onSubmit) {
                await onSubmit(data);
            } else {
                Alert.alert(
                    "Success",
                    "Beneficiary information has been saved successfully!",
                );
            }
        } catch (error) {
            console.error(
                "Error submitting form:",
                error,
            );
            Alert.alert(
                "Error",
                "Failed to save beneficiary information. Please try again.",
            );
        }
    };

    return (
        <FormProvider {...form}>
            <SafeAreaView style={{ flex: 1 }}>
                <Stack.Screen
                    options={{
                        title: beneficiary
                            ? "EDIT BENEFICIARY"
                            : "ADD BENEFICIARY",
                        headerShown: true,
                    }}
                />
                <ScrollView
                    showsVerticalScrollIndicator={
                        false
                    }
                >
                    <YStack
                        gap="$4"
                        p="$4"
                        pb="$10"
                    >
                        <PersonalDetailsSection />

                        <AddressSection />

                        <MedicalHistorySection />

                        <CareNeedsSection />

                        <MedicationSection />

                        <CognitiveFunctionSection />

                        <EmergencyContactSection />

                        <DocumentsSection />

                        <Button
                            onPress={form.handleSubmit(
                                handleSubmit,
                            )}
                            disabled={
                                form.formState
                                    .isSubmitting
                            }
                            size="$5"
                            mt="$8"
                            theme="blue"
                            fontSize="$5"
                            fontWeight="600"
                        >
                            {form.formState
                                .isSubmitting
                                ? "Submitting..."
                                : beneficiary
                                  ? "Update Beneficiary"
                                  : "Save Beneficiary"}
                        </Button>
                    </YStack>
                </ScrollView>
            </SafeAreaView>
        </FormProvider>
    );
};

export default BeneficiaryForm;
