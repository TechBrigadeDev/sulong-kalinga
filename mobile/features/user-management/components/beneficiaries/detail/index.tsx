import { Stack } from "expo-router";
import { SafeAreaView } from "react-native";
import { ScrollView, YStack } from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

import AssignedCareWorker from "./AssignedCareWorker";
import BeneficiaryHeader from "./BeneficiaryHeader";
import CareNeeds from "./CareNeeds";
import CognitiveFunctionAndMobility from "./CognitiveFunctionAndMobility";
import EmergencyContact from "./EmergencyContact";
import EmotionalWellbeing from "./EmotionalWellbeing";
import MedicalHistory from "./MedicalHistory";
import MedicationManagement from "./MedicationManagement";
import PersonalInformation from "./PersonalInformation";

interface IDetailProps {
    beneficiary: IBeneficiary;
}

const BeneficiaryDetail = ({ beneficiary }: IDetailProps) => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <Stack.Screen
                options={{
                    title: "VIEW BENEFICIARY PROFILE DETAILS",
                    headerShown: true,
                }}
            />
            <ScrollView>
                <YStack gap="$4" style={{ padding: 16 }}>
                    <BeneficiaryHeader beneficiary={beneficiary} />

                    <YStack gap="$4">
                        <PersonalInformation beneficiary={beneficiary} />
                        <MedicalHistory beneficiary={beneficiary} />
                    </YStack>

                    <YStack gap="$4">
                        <EmergencyContact beneficiary={beneficiary} />
                        <MedicationManagement beneficiary={beneficiary} />
                    </YStack>

                    <CareNeeds beneficiary={beneficiary} />
                    <CognitiveFunctionAndMobility beneficiary={beneficiary} />
                    <EmotionalWellbeing beneficiary={beneficiary} />
                    <AssignedCareWorker beneficiary={beneficiary} />
                </YStack>
            </ScrollView>
        </SafeAreaView>
    );
};

export default BeneficiaryDetail;
