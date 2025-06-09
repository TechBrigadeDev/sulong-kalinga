import { Card, H3, Text, YStack } from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    beneficiary: IBeneficiary;
}

const CareInformation = ({
    beneficiary,
}: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Care Information</H3>
            </Card.Header>
            <YStack p="$4">
                <YStack gap="$3">
                    <YStack>
                        <Text opacity={0.6}>
                            Primary Caregiver
                        </Text>
                        <Text>
                            {
                                beneficiary.primary_caregiver
                            }
                        </Text>
                    </YStack>
                    <YStack>
                        <Text opacity={0.6}>
                            Documents
                        </Text>
                        <Text>
                            Care Service
                            Agreement:{" "}
                            {beneficiary.care_service_agreement_doc
                                ? "Available"
                                : "Not Available"}
                        </Text>
                        <Text>
                            General Care Plan:{" "}
                            {beneficiary.general_care_plan_doc
                                ? "Available"
                                : "Not Available"}
                        </Text>
                    </YStack>
                </YStack>
            </YStack>
        </Card>
    );
};

export default CareInformation;
