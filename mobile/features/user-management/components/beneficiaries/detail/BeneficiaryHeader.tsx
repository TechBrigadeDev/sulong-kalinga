import AvatarImage from "components/Avatar";
import {
    Calendar,
    User,
} from "lucide-react-native";
import {
    Avatar,
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

const BeneficiaryHeader = ({
    beneficiary,
}: Props) => {
    const fullName = `${beneficiary.first_name} ${beneficiary.last_name}`;
    const sinceDate = new Date(
        beneficiary.created_at,
    ).toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
    });

    return (
        <Card
            elevate
            mb="$4"
            p="$4"
            style={{ borderRadius: 16 }}
            backgroundColor="$background"
            borderColor="$borderColor"
        >
            <YStack gap="$3">
                <XStack
                    style={{
                        alignItems: "center",
                        justifyContent: "center",
                    }}
                    gap="$4"
                >
                    <Avatar size="$10" circular>
                        <AvatarImage
                            uri={
                                beneficiary.photo
                            }
                            fallback={beneficiary.beneficiary_id.toString()}
                        />
                    </Avatar>
                    <YStack
                        flex={1}
                        style={{
                            alignItems: "center",
                        }}
                    >
                        <H3
                            color="#111827"
                            style={{
                                textAlign:
                                    "center",
                            }}
                            numberOfLines={2}
                        >
                            {fullName}
                        </H3>
                        <XStack
                            style={{
                                alignItems:
                                    "center",
                            }}
                            gap="$2"
                            mt="$2"
                        >
                            <Calendar
                                size={16}
                                color="#6b7280"
                            />
                            <Text
                                fontSize="$3"
                                style={{
                                    color: "#6b7280",
                                }}
                            >
                                {
                                    "Beneficiary since "
                                }
                                {sinceDate}
                            </Text>
                        </XStack>
                    </YStack>
                </XStack>

                <YStack
                    style={{
                        backgroundColor:
                            "#dbeafe",
                        padding: 12,
                        borderRadius: 8,
                        alignItems: "center",
                    }}
                    gap="$2"
                >
                    <User
                        size={20}
                        color="#3b82f6"
                    />
                    <Text
                        fontSize="$4"
                        style={{
                            color: "#1e40af",
                            textAlign: "center",
                        }}
                        fontWeight="500"
                    >
                        {"Beneficiary ID: "}
                        {
                            beneficiary.beneficiary_id
                        }
                    </Text>
                </YStack>
            </YStack>
        </Card>
    );
};

export default BeneficiaryHeader;
