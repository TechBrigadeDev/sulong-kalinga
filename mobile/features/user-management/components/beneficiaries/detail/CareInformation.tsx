import { FileText } from "lucide-react-native";
import {
    Card,
    H3,
    Text,
    XStack,
    YStack,
} from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    beneficiary: IBeneficiary;
}

const CareInformation = ({
    beneficiary,
}: Props) => {
    return (
        <Card
            elevate
            mb="$4"
            style={{ borderRadius: 16 }}
            backgroundColor="$background"
            borderColor="$borderColor"
        >
            <YStack gap="$4" p="$4">
                <XStack
                    gap="$3"
                    style={{
                        alignItems: "center",
                    }}
                >
                    <FileText
                        size={24}
                        color="#dc2626"
                    />
                    <H3
                        color="#111827"
                        fontWeight="600"
                    >
                        Care Information
                    </H3>
                </XStack>
                <YStack gap="$4">
                    <YStack>
                        <Text
                            fontSize="$3"
                            style={{
                                color: "#6b7280",
                            }}
                            fontWeight="500"
                            mb="$2"
                        >
                            Primary Caregiver
                        </Text>
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#111827",
                            }}
                            fontWeight="400"
                        >
                            {
                                beneficiary.primary_caregiver
                            }
                        </Text>
                    </YStack>
                    <YStack>
                        <Text
                            fontSize="$3"
                            style={{
                                color: "#6b7280",
                            }}
                            fontWeight="500"
                            mb="$3"
                        >
                            Documents
                        </Text>
                        <YStack gap="$2">
                            <XStack
                                gap="$3"
                                style={{
                                    alignItems:
                                        "center",
                                }}
                            >
                                <Text
                                    fontSize="$4"
                                    style={{
                                        color: "#111827",
                                    }}
                                    fontWeight="500"
                                    flex={1}
                                >
                                    Care Service
                                    Agreement:
                                </Text>
                                <Text
                                    fontSize="$4"
                                    style={{
                                        color: beneficiary.care_service_agreement_doc
                                            ? "#059669"
                                            : "#dc2626",
                                    }}
                                    fontWeight="600"
                                >
                                    {beneficiary.care_service_agreement_doc
                                        ? "Available"
                                        : "Not Available"}
                                </Text>
                            </XStack>
                            <XStack
                                gap="$3"
                                style={{
                                    alignItems:
                                        "center",
                                }}
                            >
                                <Text
                                    fontSize="$4"
                                    style={{
                                        color: "#111827",
                                    }}
                                    fontWeight="500"
                                    flex={1}
                                >
                                    General Care
                                    Plan:
                                </Text>
                                <Text
                                    fontSize="$4"
                                    style={{
                                        color: beneficiary.general_care_plan_doc
                                            ? "#059669"
                                            : "#dc2626",
                                    }}
                                    fontWeight="600"
                                >
                                    {beneficiary.general_care_plan_doc
                                        ? "Available"
                                        : "Not Available"}
                                </Text>
                            </XStack>
                        </YStack>
                    </YStack>
                </YStack>
            </YStack>
        </Card>
    );
};

export default CareInformation;
