import { UserCheck } from "lucide-react-native";
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

const AssignedCareWorker = ({
    beneficiary: _beneficiary,
}: Props) => {
    const tasks = [
        "Ut voluptas earum non.",
        "Explicabo ut numquam hic sit.",
        "Enim molestias autem molestiae doloremque odio rerum.",
        "Rerum minus aliquam quasi tempora quibusdam quae velit.",
        "Culpa excepturi sed rem suscipit quibusdam.",
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
                    <UserCheck
                        size={24}
                        color="#059669"
                    />
                    <H3
                        color="#111827"
                        fontWeight="600"
                    >
                        Assigned Care Worker
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
                            Name
                        </Text>
                        <Text
                            fontSize="$4"
                            style={{
                                color: "#111827",
                            }}
                            fontWeight="400"
                        >
                            Leta Nolan
                        </Text>
                    </YStack>
                    <YStack>
                        <Text
                            fontSize="$3"
                            style={{
                                color: "#6b7280",
                            }}
                            fontWeight="500"
                            mb="$2"
                        >
                            Tasks and
                            Responsibilities
                        </Text>
                        <YStack gap="$2">
                            {tasks.map(
                                (task, index) => (
                                    <XStack
                                        key={
                                            index
                                        }
                                        gap="$2"
                                        style={{
                                            alignItems:
                                                "flex-start",
                                        }}
                                    >
                                        <Text
                                            fontSize="$4"
                                            style={{
                                                color: "#059669",
                                            }}
                                            fontWeight="600"
                                        >
                                            •
                                        </Text>
                                        <Text
                                            fontSize="$4"
                                            style={{
                                                color: "#111827",
                                            }}
                                            fontWeight="400"
                                            flex={
                                                1
                                            }
                                        >
                                            {task}
                                        </Text>
                                    </XStack>
                                ),
                            )}
                        </YStack>
                    </YStack>
                </YStack>
            </YStack>
        </Card>
    );
};

export default AssignedCareWorker;
