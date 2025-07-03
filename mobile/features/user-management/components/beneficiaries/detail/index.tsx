import TabScroll from "components/tabs/TabScroll";
import { Stack } from "expo-router";
import { YStack } from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

import AssignedCareWorker from "./AssignedCareWorker";
import BeneficiaryHeader from "./BeneficiaryHeader";
import CareInformation from "./CareInformation";
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

const BeneficiaryDetail = ({
    beneficiary,
}: IDetailProps) => {
    const fullName = `${beneficiary.first_name} ${beneficiary.last_name}`;

    return (
        <>
            <Stack.Screen
                options={{
                    headerTitle: fullName,
                    headerShown: true,
                }}
            />
            <TabScroll
                flex={1}
                style={{
                    backgroundColor: "#f9fafb",
                }}
                contentContainerStyle={{
                    paddingBlockEnd: 110,
                }}
            >
                <YStack gap="$4" p="$4">
                    <BeneficiaryHeader
                        beneficiary={beneficiary}
                    />
                    <PersonalInformation
                        beneficiary={beneficiary}
                    />
                    <CareInformation
                        beneficiary={beneficiary}
                    />
                    <MedicalHistory
                        beneficiary={beneficiary}
                    />
                    <EmergencyContact
                        beneficiary={beneficiary}
                    />
                    <MedicationManagement
                        beneficiary={beneficiary}
                    />
                    <CareNeeds
                        beneficiary={beneficiary}
                    />
                    <CognitiveFunctionAndMobility
                        beneficiary={beneficiary}
                    />
                    <EmotionalWellbeing
                        beneficiary={beneficiary}
                    />
                    <AssignedCareWorker
                        beneficiary={beneficiary}
                    />
                </YStack>
            </TabScroll>
        </>
    );
};

export default BeneficiaryDetail;
