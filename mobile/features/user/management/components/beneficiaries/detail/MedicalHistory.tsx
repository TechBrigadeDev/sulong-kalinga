import { Card, H3, Text, XStack, YStack } from "tamagui";
import { IBeneficiary } from "../../../../user.schema";

interface Props {
    beneficiary: IBeneficiary;
}

const MedicalHistory = ({ beneficiary }: Props) => {
    const medicalInfo = {
        "Medical Conditions": "Et voluptas repudiandae qui voluptatem quod neque fugiat.",
        "Medications": "Quos aliquam nulla qui facilis.",
        "Allergies": "Repellendus cum est eum natus ab.",
        "Immunizations": "Ea numquam rerum sit rerum consequatur corrupti et.",
        "Category": "Dementia"
    };

    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Medical History</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack gap="$3">
                    {Object.entries(medicalInfo).map(([key, value]) => (
                        <YStack key={key}>
                            <Text opacity={0.6}>{key}</Text>
                            <Text>{value}</Text>
                        </YStack>
                    ))}
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default MedicalHistory;
