import { Button, ScrollView, YStack } from "tamagui";
import { IBeneficiary } from "../../../user.schema";
import { useState } from "react";
import { SafeAreaView } from "react-native";
import PersonalDetailsSection from "./PersonalDetailsSection";
import AddressSection from "./AddressSection";
import MedicalHistorySection from "./MedicalHistorySection";
import CareNeedsSection from "./CareNeedsSection";
import MedicationSection from "./MedicationSection";
import CognitiveFunctionSection from "./CognitiveFunctionSection";
import EmergencyContactSection from "./EmergencyContactSection";
import DocumentsSection from "./DocumentsSection";

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
        setFormData(prev => ({
            ...prev,
            [field]: value
        }));
    };

    return (
        <SafeAreaView style={{ flex: 1 }}>
            <ScrollView>
                <YStack style={{ padding: 16 }} space="$4">
                    <PersonalDetailsSection
                        data={formData}
                        onChange={handleChange}
                    />
                    
                    <AddressSection
                        data={formData}
                        onChange={handleChange}
                    />
                    
                    <MedicalHistorySection
                        data={formData}
                        onChange={handleChange}
                    />
                    
                    <CareNeedsSection
                        data={formData}
                        onChange={handleChange}
                    />
                    
                    <MedicationSection
                        data={formData}
                        onChange={handleChange}
                    />
                    
                    <CognitiveFunctionSection
                        data={formData}
                        onChange={handleChange}
                    />
                    
                    <EmergencyContactSection
                        data={formData}
                        onChange={handleChange}
                    />
                    
                    <DocumentsSection
                        data={formData}
                        onChange={handleChange}
                    />
                    
                    <Button 
                        theme="blue"
                        size="$4"
                        onPress={handleSubmit}
                    >
                        Save Beneficiary
                    </Button>
                </YStack>
            </ScrollView>
        </SafeAreaView>
    );
};

export default BeneficiaryForm;