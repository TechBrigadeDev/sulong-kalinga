import { Card, H3, Text, YStack } from "tamagui";
import { IBeneficiary } from "~/features/user/management/management.type";

interface Props {
    beneficiary: IBeneficiary;
}

const AssignedCareWorker = ({ beneficiary }: Props) => {
    const tasks = [
        "Ut voluptas earum non.",
        "Explicabo ut numquam hic sit.",
        "Enim molestias autem molestiae doloremque odio rerum.",
        "Rerum minus aliquam quasi tempora quibusdam quae velit.",
        "Culpa excepturi sed rem suscipit quibusdam."
    ];

    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Assigned Care Worker</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack gap="$3">
                    <YStack>
                        <Text opacity={0.6}>Name</Text>
                        <Text>Leta Nolan</Text>
                    </YStack>
                    <YStack>
                        <Text opacity={0.6}>Tasks and Responsibilities</Text>
                        {tasks.map((task, index) => (
                            <Text key={index} marginTop="$2">• {task}</Text>
                        ))}
                    </YStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default AssignedCareWorker;
