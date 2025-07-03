import { Heart } from "lucide-react-native";
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

const MedicalHistory = ({
    beneficiary: _beneficiary,
}: Props) => {
    const medicalInfo = {
        "Medical Conditions":
            "Et voluptas repudiandae qui voluptatem quod neque fugiat.",
        Medications:
            "Quos aliquam nulla qui facilis.",
        Allergies:
            "Repellendus cum est eum natus ab.",
        Immunizations:
            "Ea numquam rerum sit rerum consequatur corrupti et.",
        Category: "Dementia",
    };

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
                    <Heart
                        size={24}
                        color="#3b82f6"
                    />
                    <H3
                        color="#111827"
                        fontWeight="600"
                    >
                        Medical History
                    </H3>
                </XStack>
                <YStack gap="$4">
                    {Object.entries(
                        medicalInfo,
                    ).map(([key, value]) => (
                        <YStack key={key}>
                            <Text
                                fontSize="$3"
                                style={{
                                    color: "#6b7280",
                                }}
                                fontWeight="500"
                                mb="$2"
                            >
                                {key}
                            </Text>
                            <Text
                                fontSize="$4"
                                style={{
                                    color: "#111827",
                                }}
                                fontWeight="400"
                            >
                                {value}
                            </Text>
                        </YStack>
                    ))}
                </YStack>
            </YStack>
        </Card>
    );
};

export default MedicalHistory;
