import { Pill } from "lucide-react-native";
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

const MedicationManagement = ({
    beneficiary: _beneficiary,
}: Props) => {
    const medications = [
        {
            name: "sit",
            dosage: "rerum",
            frequency: "voluptatem",
            instructions: "Et et aut numquam.",
        },
        {
            name: "aut",
            dosage: "ut",
            frequency: "autem",
            instructions:
                "Natus sint ratione voluptatem error eaque nobis facere atque.",
        },
        {
            name: "recusandae",
            dosage: "dolores",
            frequency: "voluptate",
            instructions:
                "Repellat et aut harum aliquam sunt suscipit.",
        },
    ];

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
                    <Pill
                        size={24}
                        color="#3b82f6"
                    />
                    <H3
                        color="#111827"
                        fontWeight="600"
                    >
                        Medication Management
                    </H3>
                </XStack>
                <YStack gap="$4">
                    {medications.map(
                        (med, index) => (
                            <Card
                                key={index}
                                style={{
                                    backgroundColor:
                                        "#dbeafe",
                                    borderRadius: 12,
                                    padding: 16,
                                }}
                                borderColor="$borderColor"
                            >
                                <YStack gap="$3">
                                    <Text
                                        fontSize="$5"
                                        color="#1e40af"
                                        fontWeight="600"
                                        textTransform="capitalize"
                                    >
                                        {med.name}
                                    </Text>
                                    <XStack gap="$4">
                                        <YStack
                                            flex={
                                                1
                                            }
                                        >
                                            <Text
                                                fontSize="$3"
                                                style={{
                                                    color: "#6b7280",
                                                }}
                                                fontWeight="500"
                                                mb="$1"
                                            >
                                                Dosage
                                            </Text>
                                            <Text
                                                fontSize="$4"
                                                style={{
                                                    color: "#111827",
                                                }}
                                                fontWeight="400"
                                            >
                                                {
                                                    med.dosage
                                                }
                                            </Text>
                                        </YStack>
                                        <YStack
                                            flex={
                                                1
                                            }
                                        >
                                            <Text
                                                fontSize="$3"
                                                style={{
                                                    color: "#6b7280",
                                                }}
                                                fontWeight="500"
                                                mb="$1"
                                            >
                                                Frequency
                                            </Text>
                                            <Text
                                                fontSize="$4"
                                                style={{
                                                    color: "#111827",
                                                }}
                                                fontWeight="400"
                                            >
                                                {
                                                    med.frequency
                                                }
                                            </Text>
                                        </YStack>
                                    </XStack>
                                    <YStack>
                                        <Text
                                            fontSize="$3"
                                            style={{
                                                color: "#6b7280",
                                            }}
                                            fontWeight="500"
                                            mb="$1"
                                        >
                                            Instructions
                                        </Text>
                                        <Text
                                            fontSize="$4"
                                            style={{
                                                color: "#111827",
                                            }}
                                            fontWeight="400"
                                        >
                                            {
                                                med.instructions
                                            }
                                        </Text>
                                    </YStack>
                                </YStack>
                            </Card>
                        ),
                    )}
                </YStack>
            </YStack>
        </Card>
    );
};

export default MedicationManagement;
