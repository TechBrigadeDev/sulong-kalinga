import { Shield } from "lucide-react-native";
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

const CareNeeds = ({
    beneficiary: _beneficiary,
}: Props) => {
    const careNeeds = [
        {
            type: "Mobility",
            frequency: "dolore",
            assistance:
                "Sit quia dolorum eveniet expedita in repellat adit sunt.",
        },
        {
            type: "Cognitive / Communication",
            frequency: "magnam",
            assistance:
                "Porro consequatur est corrupti expedita et.",
        },
        {
            type: "Self-sustainability",
            frequency: "saepe",
            assistance:
                "Voluptatibus minus et fugit voluptatem.",
        },
        {
            type: "Disease / Therapy Handling",
            frequency: "omnis",
            assistance:
                "Voluptate unde quo pariatur quas sit est aut.",
        },
        {
            type: "Daily Life / Social Contact",
            frequency: "aliquam",
            assistance:
                "Eum hic natus sapiente est voluptatibus eos asperiores.",
        },
        {
            type: "Outdoor Activities",
            frequency: "excepturi",
            assistance:
                "At vero reprehenderit quia dolorem est est.",
        },
        {
            type: "Household Keeping",
            frequency: "voluptas",
            assistance:
                "Suscipit consectetur pariatur porro minus consectetur ipsa.",
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
                    <Shield
                        size={24}
                        color="#16a34a"
                    />
                    <H3
                        color="#111827"
                        fontWeight="600"
                    >
                        Care Needs
                    </H3>
                </XStack>
                <YStack gap="$4">
                    {careNeeds.map(
                        (need, index) => (
                            <Card
                                key={index}
                                style={{
                                    backgroundColor:
                                        "#f3f4f6",
                                    borderRadius: 12,
                                    padding: 16,
                                }}
                                borderColor="$borderColor"
                            >
                                <YStack gap="$3">
                                    <Text
                                        fontSize="$5"
                                        color="#111827"
                                        fontWeight="600"
                                    >
                                        {
                                            need.type
                                        }
                                    </Text>
                                    <YStack gap="$2">
                                        <Text
                                            fontSize="$3"
                                            style={{
                                                color: "#6b7280",
                                            }}
                                            fontWeight="500"
                                        >
                                            {
                                                "Frequency: "
                                            }
                                            {
                                                need.frequency
                                            }
                                        </Text>
                                        <Text
                                            fontSize="$4"
                                            style={{
                                                color: "#111827",
                                            }}
                                            fontWeight="400"
                                        >
                                            {
                                                need.assistance
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

export default CareNeeds;
