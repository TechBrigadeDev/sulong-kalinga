import { Ionicons } from "@expo/vector-icons";
import ScrollView from "components/ScrollView";
import { Stack } from "expo-router";
import { useState } from "react";
import { Button, Form, YStack } from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

import { AddressSection } from "./components/AddressSection";
import { CareNeedsSection } from "./components/CareNeedsSection";
import { CognitiveFunctionSection } from "./components/CognitiveFunctionSection";
import { DocumentsSection } from "./components/DocumentsSection";
import { EmergencyContactSection } from "./components/EmergencyContactSection";
import { MedicalHistorySection } from "./components/MedicalHistorySection";
import { MedicationSection } from "./components/MedicationSection";
import { PersonalDetailsSection } from "./components/PersonalDetailsSection";
import { SafeAreaView } from "react-native-safe-area-context";

interface Props {
    beneficiary?: IBeneficiary;
    onSubmit?: (
        data: Partial<IBeneficiary>,
    ) => void;
}

const initialFormData: Partial<IBeneficiary> = {
    first_name: "",
    last_name: "",
    birthday: "",
    gender: "",
    civil_status: "",
    primary_caregiver: "",
    mobile: "",
    street_address: "",
    municipality_id: undefined,
    barangay_id: undefined,
    medical_conditions: "",
    medications: "",
    allergies: "",
    immunizations: "",
    photo: "",
    care_service_agreement_doc: "",
    general_care_plan_doc: "",
    beneficiary_signature: "",
    care_worker_signature: "",
    emergency_contact_name: "",
    emergency_contact_relation: "",
    emergency_contact_mobile: "",
    emergency_procedure: "",
    medications_list: [],
    // Mobility fields
    walking_ability: "",
    assistive_devices: "",
    transportation_needs: "",
    // Cognitive fields
    memory: "",
    thinking_skills: "",
    orientation: "",
    behavior: "",
    // Emotional fields
    mood: "",
    social_interactions: "",
    emotional_support_need: "",
    // Care needs fields
    mobility_frequency: "",
    mobility_assistance: "",
    cognitive_frequency: "",
    cognitive_assistance: "",
    self_sustainability_frequency: "",
    self_sustainability_assistance: "",
    disease_therapy_frequency: "",
    disease_therapy_assistance: "",
    daily_life_frequency: "",
    daily_life_assistance: "",
    outdoor_frequency: "",
    outdoor_assistance: "",
    household_frequency: "",
    household_assistance: "",
};

const BeneficiaryForm = ({
    beneficiary,
    onSubmit,
}: Props) => {
    const [formData, setFormData] = useState<
        Partial<IBeneficiary>
    >(beneficiary || initialFormData);

    const handleSubmit = () => {
        console.log("Form data:", formData);
        onSubmit?.(formData);
    };

    const handleChange = (
        field: keyof IBeneficiary,
        value: any,
    ) => {
        setFormData(
            (prev: Partial<IBeneficiary>) => ({
                ...prev,
                [field]: value,
            }),
        );
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
                <Form onSubmit={handleSubmit}>
                    <YStack gap="$4" p="$4">
                        <PersonalDetailsSection
                            data={formData}
                            onChange={
                                handleChange
                            }
                        />
                        <AddressSection
                            data={formData}
                            onChange={
                                handleChange
                            }
                        />
                        <MedicalHistorySection
                            data={formData}
                            onChange={
                                handleChange
                            }
                        />
                        <CareNeedsSection
                            data={formData}
                            onChange={
                                handleChange
                            }
                        />
                        <MedicationSection
                            data={formData}
                            onChange={
                                handleChange
                            }
                        />
                        <CognitiveFunctionSection
                            data={formData}
                            onChange={
                                handleChange
                            }
                        />
                        <EmergencyContactSection
                            data={formData}
                            onChange={
                                handleChange
                            }
                        />
                        <DocumentsSection
                            data={formData}
                            onChange={
                                handleChange
                            }
                        />

                        <Button
                            theme="green"
                            size="$5"
                            icon={
                                <Ionicons
                                    name="save-outline"
                                    size={20}
                                    color="white"
                                />
                            }
                            onPress={handleSubmit}
                        >
                            {beneficiary
                                ? "Update"
                                : "Save"}{" "}
                            Beneficiary
                        </Button>
                    </YStack>
                </Form>
            </ScrollView>
        </SafeAreaView>
    );
};

export default BeneficiaryForm;
