import { SafeAreaView } from "react-native";
import { ScrollView, YStack } from "tamagui";
import { IBeneficiary } from "~/features/user/management/management.type";
import { Stack } from "expo-router";
import BeneficiaryHeader from "./BeneficiaryHeader";
import PersonalInformation from "./PersonalInformation";
import MedicalHistory from "./MedicalHistory";
import EmergencyContact from "./EmergencyContact";
import MedicationManagement from "./MedicationManagement";
import CareNeeds from "./CareNeeds";
import CognitiveFunctionAndMobility from "./CognitiveFunctionAndMobility";
import EmotionalWellbeing from "./EmotionalWellbeing";
import AssignedCareWorker from "./AssignedCareWorker";

interface IDetailProps {
    beneficiary: IBeneficiary
}

const BeneficiaryDetail = ({
    beneficiary
}: IDetailProps) => {
    return (
        <SafeAreaView style={{ flex: 1 }}>
            <Stack.Screen options={{
                title: "VIEW BENEFICIARY PROFILE DETAILS",
                headerShown: true,
            }} />
            <ScrollView>
                <YStack space="$4" style={{ padding: 16 }}>
                    <BeneficiaryHeader beneficiary={beneficiary} />
                    
                    <YStack space="$4">
                        <PersonalInformation beneficiary={beneficiary} />
                        <MedicalHistory beneficiary={beneficiary} />
                    </YStack>
                    
                    <YStack space="$4">
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
    )
}

export default BeneficiaryDetail;