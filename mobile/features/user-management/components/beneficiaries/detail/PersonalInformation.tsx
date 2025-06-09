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
        <Card elevate>
            <Card.Header padded>
                <H3>Personal Information</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack gap="$3">
                    <XStack gap="$4">
                        <YStack flex={1}>
                            <Text opacity={0.6}>
                                Full Name
                            </Text>
                            <Text>
                                {
                                    beneficiary.first_name
                                }{" "}
                                {
                                    beneficiary.last_name
                                }
                            </Text>
                        </YStack>
                        <YStack flex={1}>
                            <Text opacity={0.6}>
                                Civil Status
                            </Text>
                            <Text>
                                {
                                    beneficiary.civil_status
                                }
                            </Text>
                        </YStack>
                    </XStack>
                    <XStack gap="$4">
                        <YStack flex={1}>
                            <Text opacity={0.6}>
                                Gender
                            </Text>
                            <Text>
                                {
                                    beneficiary.gender
                                }
                            </Text>
                        </YStack>
                        <YStack flex={1}>
                            <Text opacity={0.6}>
                                Birthday
                            </Text>
                            <Text>
                                {new Date(
                                    beneficiary.birthday,
                                ).toLocaleDateString()}
                            </Text>
                        </YStack>
                    </XStack>
                    <YStack>
                        <Text opacity={0.6}>
                            Address
                        </Text>
                        <Text>
                            {
                                beneficiary.street_address
                            }
                        </Text>
                    </YStack>
                    <XStack gap="$4">
                        <YStack flex={1}>
                            <Text opacity={0.6}>
                                Mobile
                            </Text>
                            <Text>
                                {
                                    beneficiary.mobile
                                }
                            </Text>
                        </YStack>
                    </XStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default PersonalInformation;
