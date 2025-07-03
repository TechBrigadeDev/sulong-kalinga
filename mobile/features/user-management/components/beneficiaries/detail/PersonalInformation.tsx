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

const PersonalInformation = ({
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
                <H3
                    color="#111827"
                    fontWeight="600"
                >
                    Personal Information
                </H3>
                <YStack gap="$4">
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
                                Full Name
                            </Text>
                            <Text
                                fontSize="$4"
                                style={{
                                    color: "#111827",
                                }}
                                fontWeight="400"
                            >
                                {
                                    beneficiary.first_name
                                }{" "}
                                {
                                    beneficiary.last_name
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
                                Civil Status
                            </Text>
                            <Text
                                fontSize="$4"
                                style={{
                                    color: "#111827",
                                }}
                                fontWeight="400"
                            >
                                {
                                    beneficiary.civil_status
                                }
                            </Text>
                        </YStack>
                    </XStack>
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
                                Gender
                            </Text>
                            <Text
                                fontSize="$4"
                                style={{
                                    color: "#111827",
                                }}
                                fontWeight="400"
                            >
                                {
                                    beneficiary.gender
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
                                Birthday
                            </Text>
                            <Text
                                fontSize="$4"
                                style={{
                                    color: "#111827",
                                }}
                                fontWeight="400"
                            >
                                {new Date(
                                    beneficiary.birthday,
                                ).toLocaleDateString()}
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
                            Address
                        </Text>
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#111827",
                            }}
                            fontWeight="400"
                        >
                            {
                                beneficiary.street_address
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
                                    beneficiary.mobile
                                }
                            </Text>
                        </YStack>
                    </XStack>
                </YStack>
            </YStack>
        </Card>
    );
};

export default PersonalInformation;
