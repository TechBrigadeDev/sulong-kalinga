import { Card, H3, Text, XStack, YStack } from "tamagui";
import { IBeneficiary } from "~/features/user/management/management.type";

interface Props {
    beneficiary: IBeneficiary;
}

const EmergencyContact = ({ beneficiary }: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Emergency Contact</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack gap="$3">
                    <YStack>
                        <Text opacity={0.6}>Contact Name</Text>
                        <Text>{beneficiary.emergency_contact_name}</Text>
                    </YStack>
                    <XStack gap="$4">
                        <YStack flex={1}>
                            <Text opacity={0.6}>Relation</Text>
                            <Text>{beneficiary.emergency_contact_relation}</Text>
                        </YStack>
                        <YStack flex={1}>
                            <Text opacity={0.6}>Mobile</Text>
                            <Text>{beneficiary.emergency_contact_mobile}</Text>
                        </YStack>
                    </XStack>
                    <YStack>
                        <Text opacity={0.6}>Emergency Procedure</Text>
                        <Text>{beneficiary.emergency_procedure}</Text>
                    </YStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default EmergencyContact;
