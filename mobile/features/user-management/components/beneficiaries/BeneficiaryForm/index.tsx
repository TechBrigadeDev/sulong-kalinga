import ScrollView from "components/ScrollView";
import { Stack } from "expo-router";
import { SafeAreaView } from "react-native-safe-area-context";
import { Button, YStack } from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

import { PersonalDetailsSection } from "./components/PersonalDetailsSection";
import {
    beneficiaryFormOpts,
    useBeneficiaryForm,
} from "./form";

interface Props {
    beneficiary?: IBeneficiary;
    onSubmit?: (
        data: Partial<IBeneficiary>,
    ) => Promise<void>;
}

const BeneficiaryForm = ({
    beneficiary,
    onSubmit,
}: Props) => {
    const form = useBeneficiaryForm({
        ...beneficiaryFormOpts,
        onSubmit: async (data) => {
            console.log(
                "Submitting beneficiary data:",
                data,
            );
        },
    });

    return (
        <SafeAreaView style={{ flex: 1 }}>
            <Stack.Screen
                options={{
                    title: beneficiary
                        ? "EDIT BENEFICIARY"
                        : "ADD BENEFICIARY",
                    headerShown: true,
                }}
            />
            <ScrollView>
                <YStack gap="$4" p="$4">
                    <PersonalDetailsSection
                        form={form}
                    />
                    <form.AppForm>
                        <form.Subscribe
                            selector={(state) =>
                                state.isSubmitting
                            }
                        >
                            {(isSubmitting) => (
                                <Button
                                    disabled={
                                        isSubmitting
                                    }
                                    size="$4"
                                    mt="$4"
                                >
                                    Submit
                                </Button>
                            )}
                        </form.Subscribe>
                    </form.AppForm>

                    {/* <AddressSection
                        data={{
                            street_address:
                                formData.street_address,
                            municipality_id:
                                formData.municipality_id,
                            barangay_id:
                                formData.barangay_id,
                        }}
                        onChange={
                            handleFieldChange
                        }
                    />

                    <MedicalHistorySection
                        data={{
                            medical_conditions:
                                formData.medical_conditions,
                            medications:
                                formData.medications,
                            allergies:
                                formData.allergies,
                            immunizations:
                                formData.immunizations,
                        }}
                        onChange={
                            handleFieldChange
                        }
                    />

                    <CareNeedsSection
                        data={{
                            mobility_frequency:
                                formData.mobility_frequency,
                            mobility_assistance:
                                formData.mobility_assistance,
                            cognitive_frequency:
                                formData.cognitive_frequency,
                            cognitive_assistance:
                                formData.cognitive_assistance,
                            self_sustainability_frequency:
                                formData.self_sustainability_frequency,
                            self_sustainability_assistance:
                                formData.self_sustainability_assistance,
                            disease_therapy_frequency:
                                formData.disease_therapy_frequency,
                            disease_therapy_assistance:
                                formData.disease_therapy_assistance,
                            daily_life_frequency:
                                formData.daily_life_frequency,
                            daily_life_assistance:
                                formData.daily_life_assistance,
                            outdoor_frequency:
                                formData.outdoor_frequency,
                            outdoor_assistance:
                                formData.outdoor_assistance,
                            household_frequency:
                                formData.household_frequency,
                            household_assistance:
                                formData.household_assistance,
                        }}
                        onChange={
                            handleFieldChange
                        }
                    />

                    <MedicationSection
                        data={{
                            medications_list:
                                formData.medications_list ||
                                [],
                        }}
                        onChange={
                            handleFieldChange
                        }
                    />

                    <CognitiveFunctionSection
                        data={{
                            walking_ability:
                                formData.walking_ability,
                            assistive_devices:
                                formData.assistive_devices,
                            transportation_needs:
                                formData.transportation_needs,
                            memory: formData.memory,
                            thinking_skills:
                                formData.thinking_skills,
                            orientation:
                                formData.orientation,
                            behavior:
                                formData.behavior,
                            mood: formData.mood,
                            social_interactions:
                                formData.social_interactions,
                            emotional_support_need:
                                formData.emotional_support_need,
                        }}
                        onChange={
                            handleFieldChange
                        }
                    />

                    <EmergencyContactSection
                        data={{
                            emergency_contact_name:
                                formData.emergency_contact_name,
                            emergency_contact_relation:
                                formData.emergency_contact_relation,
                            emergency_contact_mobile:
                                formData.emergency_contact_mobile,
                            emergency_procedure:
                                formData.emergency_procedure,
                        }}
                        onChange={
                            handleFieldChange
                        }
                    />

                    <DocumentsSection
                        data={{
                            photo: formData.photo,
                            care_service_agreement_doc:
                                formData.care_service_agreement_doc,
                            general_care_plan_doc:
                                formData.general_care_plan_doc,
                            beneficiary_signature:
                                formData.beneficiary_signature,
                            care_worker_signature:
                                formData.care_worker_signature,
                        }}
                        onChange={
                            handleFieldChange
                        }
                    /> */}
                </YStack>
            </ScrollView>
        </SafeAreaView>
    );
};

export default BeneficiaryForm;
