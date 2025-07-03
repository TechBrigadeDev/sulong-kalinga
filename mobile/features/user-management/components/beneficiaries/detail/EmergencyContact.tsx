import { Phone } from "lucide-react-native";
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

const EmergencyContact = ({
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
                    <Phone
                        size={24}
                        color="#ea580c"
                    />
                    <H3
                        color="#111827"
                        fontWeight="600"
                    >
                        Emergency Contact
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
                            Contact Name
                        </Text>
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#111827",
                            }}
                            fontWeight="400"
                        >
                            {
                                beneficiary.emergency_contact_name
                            }
                        </Text>
                    </YStack>
                    <XStack gap="$4">
                        <YStack flex={1}>
                            <Text
                                fontSize="$3"
                                style={{
                                    color: "#6b7280",
                                }}
                                fontWeight="500"
                                mb="$2"
                            >
                                Relation
                            </Text>
                            <Text
                                fontSize="$4"
                                style={{
                                    color: "#111827",
                                }}
                                fontWeight="400"
                            >
                                {
                                    beneficiary.emergency_contact_relation
                                }
                            </Text>
                        </YStack>
                        <YStack flex={1}>
                            <Text
                                fontSize="$3"
                                style={{
                                    color: "#6b7280",
                                }}
                                fontWeight="500"
                                mb="$2"
                            >
                                Mobile
                            </Text>
                            <Text
                                fontSize="$4"
                                style={{
                                    color: "#111827",
                                }}
                                fontWeight="400"
                            >
                                {
                                    beneficiary.emergency_contact_mobile
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
                            mb="$2"
                        >
                            Emergency Procedure
                        </Text>
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#111827",
                            }}
                            fontWeight="400"
                        >
                            {
                                beneficiary.emergency_procedure
                            }
                        </Text>
                    </YStack>
                </YStack>
            </YStack>
        </Card>
    );
};

export default EmergencyContact;
