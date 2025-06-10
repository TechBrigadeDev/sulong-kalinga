import { Ionicons } from "@expo/vector-icons";
import ScrollView from "components/ScrollView";
import { Stack } from "expo-router";
import { useState } from "react";
import { SafeAreaView } from "react-native-safe-area-context";
import { Button, Spinner, YStack } from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

import { AddressSection } from "./components/AddressSection";
import { CareNeedsSection } from "./components/CareNeedsSection";
import { CognitiveFunctionSection } from "./components/CognitiveFunctionSection";
import { DocumentsSection } from "./components/DocumentsSection";
import { EmergencyContactSection } from "./components/EmergencyContactSection";
import { MedicalHistorySection } from "./components/MedicalHistorySection";
import { MedicationSection } from "./components/MedicationSection";
import { PersonalDetailsSection } from "./components/PersonalDetailsSection";

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
    const [formData, setFormData] = useState<
        Partial<IBeneficiary>
    >({
        first_name: beneficiary?.first_name || "",
        last_name: beneficiary?.last_name || "",
        birthday: beneficiary?.birthday || "",
        gender: beneficiary?.gender || "",
        civil_status:
            beneficiary?.civil_status || "",
        primary_caregiver:
            beneficiary?.primary_caregiver || "",
        mobile: beneficiary?.mobile || "",
        street_address:
            beneficiary?.street_address || "",
        municipality_id:
            beneficiary?.municipality_id ||
            undefined,
        barangay_id:
            beneficiary?.barangay_id || undefined,
        medical_conditions:
            beneficiary?.medical_conditions || "",
        medications:
            beneficiary?.medications || "",
        allergies: beneficiary?.allergies || "",
        immunizations:
            beneficiary?.immunizations || "",
        emergency_contact_name:
            beneficiary?.emergency_contact_name ||
            "",
        emergency_contact_relation:
            beneficiary?.emergency_contact_relation ||
            "",
        emergency_contact_mobile:
            beneficiary?.emergency_contact_mobile ||
            "",
        emergency_procedure:
            beneficiary?.emergency_procedure ||
            "",
        medications_list:
            beneficiary?.medications_list || [],
        walking_ability:
            beneficiary?.walking_ability || "",
        assistive_devices:
            beneficiary?.assistive_devices || "",
        transportation_needs:
            beneficiary?.transportation_needs ||
            "",
        memory: beneficiary?.memory || "",
        thinking_skills:
            beneficiary?.thinking_skills || "",
        orientation:
            beneficiary?.orientation || "",
        behavior: beneficiary?.behavior || "",
        mood: beneficiary?.mood || "",
        social_interactions:
            beneficiary?.social_interactions ||
            "",
        emotional_support_need:
            beneficiary?.emotional_support_need ||
            "",
        mobility_frequency:
            beneficiary?.mobility_frequency || "",
        mobility_assistance:
            beneficiary?.mobility_assistance ||
            "",
        cognitive_frequency:
            beneficiary?.cognitive_frequency ||
            "",
        cognitive_assistance:
            beneficiary?.cognitive_assistance ||
            "",
        self_sustainability_frequency:
            beneficiary?.self_sustainability_frequency ||
            "",
        self_sustainability_assistance:
            beneficiary?.self_sustainability_assistance ||
            "",
        disease_therapy_frequency:
            beneficiary?.disease_therapy_frequency ||
            "",
        disease_therapy_assistance:
            beneficiary?.disease_therapy_assistance ||
            "",
        daily_life_frequency:
            beneficiary?.daily_life_frequency ||
            "",
        daily_life_assistance:
            beneficiary?.daily_life_assistance ||
            "",
        outdoor_frequency:
            beneficiary?.outdoor_frequency || "",
        outdoor_assistance:
            beneficiary?.outdoor_assistance || "",
        household_frequency:
            beneficiary?.household_frequency ||
            "",
        household_assistance:
            beneficiary?.household_assistance ||
            "",
        photo: beneficiary?.photo || "",
        care_service_agreement_doc:
            beneficiary?.care_service_agreement_doc ||
            "",
        general_care_plan_doc:
            beneficiary?.general_care_plan_doc ||
            "",
        beneficiary_signature:
            beneficiary?.beneficiary_signature ||
            "",
        care_worker_signature:
            beneficiary?.care_worker_signature ||
            "",
    });

    const [isPending, setIsPending] =
        useState(false);

    const handleFieldChange = (
        field: string | number | symbol,
        value: any,
    ) => {
        setFormData((prev) => ({
            ...prev,
            [field]: value,
        }));
    };

    const handleSubmit = async () => {
        if (onSubmit) {
            setIsPending(true);
            try {
                await onSubmit(formData);
            } catch (error) {
                console.error(
                    "Error submitting form:",
                    error,
                );
            } finally {
                setIsPending(false);
            }
        }
    };

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
                    {/* Personal Details Section */}
                    <PersonalDetailsSection
                        data={{
                            first_name:
                                formData.first_name,
                            last_name:
                                formData.last_name,
                            birthday:
                                formData.birthday,
                            gender: formData.gender,
                            civil_status:
                                formData.civil_status,
                            primary_caregiver:
                                formData.primary_caregiver,
                            mobile: formData.mobile,
                        }}
                        onChange={
                            handleFieldChange
                        }
                    />

                    {/* Other sections using the existing components */}
                    <AddressSection
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
                    />

                    <Button
                        theme="green"
                        size="$5"
                        icon={
                            isPending ? (
                                <Spinner size="small" />
                            ) : (
                                <Ionicons
                                    name="save-outline"
                                    size={20}
                                    color="white"
                                />
                            )
                        }
                        onPress={handleSubmit}
                        disabled={isPending}
                    >
                        {isPending
                            ? "Saving..."
                            : beneficiary
                              ? "Update"
                              : "Save"}{" "}
                        Beneficiary
                    </Button>
                </YStack>
            </ScrollView>
        </SafeAreaView>
    );
};

export default BeneficiaryForm;
