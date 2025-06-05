import { Card, H3, Text, XStack, YStack } from "tamagui";
import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    beneficiary: IBeneficiary;
}

const MedicationManagement = ({ beneficiary }: Props) => {
    const medications = [
        {
            name: "sit",
            dosage: "rerum",
            frequency: "voluptatem",
            instructions: "Et et aut numquam."
        },
        {
            name: "aut",
            dosage: "ut",
            frequency: "autem",
            instructions: "Natus sint ratione voluptatem error eaque nobis facere atque."
        },
        {
            name: "recusandae",
            dosage: "dolores",
            frequency: "voluptate",
            instructions: "Repellat et aut harum aliquam sunt suscipit."
        }
    ];

    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Medication Management</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack gap="$3">
                    {medications.map((med, index) => (
                        <Card key={index} bordered>
                            <Card.Header padded>
                                <Text fontSize="$5">{med.name}</Text>
                            </Card.Header>
                            <Card.Footer padded>
                                <YStack gap="$2">
                                    <XStack gap="$4">
                                        <YStack flex={1}>
                                            <Text opacity={0.6}>Dosage</Text>
                                            <Text>{med.dosage}</Text>
                                        </YStack>
                                        <YStack flex={1}>
                                            <Text opacity={0.6}>Frequency</Text>
                                            <Text>{med.frequency}</Text>
                                        </YStack>
                                    </XStack>
                                    <YStack>
                                        <Text opacity={0.6}>Instructions</Text>
                                        <Text>{med.instructions}</Text>
                                    </YStack>
                                </YStack>
                            </Card.Footer>
                        </Card>
                    ))}
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default MedicationManagement;
