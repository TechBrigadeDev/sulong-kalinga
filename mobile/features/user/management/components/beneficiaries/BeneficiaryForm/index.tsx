import { Button, ScrollView, YStack } from "tamagui";
import { IBeneficiary } from "../../../user.schema";
import { useState } from "react";
import { SafeAreaView } from "react-native";
import PersonalDetailsSection from "./components/PersonalDetailsSection";
import AddressSection from "./components/AddressSection";
import MedicalHistorySection from "./components/MedicalHistorySection";
import CareNeedsSection from "./components/CareNeedsSection";
import MedicationSection from "./components/MedicationSection";
import CognitiveFunctionSection from "./components/CognitiveFunctionSection";
import EmergencyContactSection from "./components/EmergencyContactSection";
import DocumentsSection from "./components/DocumentsSection";
import { Stack } from "expo-router";
import { Ionicons } from "@expo/vector-icons";

interface Props {
    beneficiary?: IBeneficiary;
    onSubmit?: (data: Partial<IBeneficiary>) => void;
}

const BeneficiaryForm = ({ beneficiary, onSubmit }: Props) => {
    const [formData, setFormData] = useState<Partial<IBeneficiary>>(beneficiary || {});

    const handleSubmit = () => {
        console.log("Form data:", formData);
        onSubmit?.(formData);
    };

    const handleChange = (field: keyof IBeneficiary, value: any) => {
        setFormData((prev: Partial<IBeneficiary>) => ({
            ...prev,
            [field]: value
        }));
    };

    return (
        <SafeAreaView style={{ flex: 1 }}>
            <Stack.Screen options={{
                title: beneficiary ? "EDIT BENEFICIARY" : "ADD BENEFICIARY",
                headerShown: true,
            }} />
            <ScrollView>
                <YStack space="$4" p="$4">
                    <PersonalDetailsSection data={formData} onChange={handleChange} />
                    <AddressSection data={formData} onChange={handleChange} />
                    <MedicalHistorySection data={formData} onChange={handleChange} />
                    <CareNeedsSection data={formData} onChange={handleChange} />
                    <MedicationSection data={formData} onChange={handleChange} />
                    <CognitiveFunctionSection data={formData} onChange={handleChange} />
                    <EmergencyContactSection data={formData} onChange={handleChange} />
                    <DocumentsSection data={formData} onChange={handleChange} />

                    <Button 
                        theme="green"
                        size="$5"
                        icon={<Ionicons name="save-outline" size={20} color="white" />}
                        onPress={handleSubmit}
                    >
                        {beneficiary ? "Update" : "Save"} Beneficiary
                    </Button>
                </YStack>
            </ScrollView>
        </SafeAreaView>
    );
};

export default BeneficiaryForm;