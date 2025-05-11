import { Card, H3, Text, XStack, YStack } from "tamagui";
import { IFamilyMember } from "../../../../../user.schema";

interface Props {
    familyMember: IFamilyMember;
}

const PersonalInformation = ({ familyMember }: Props) => {
    return (
        <Card elevate>
            <Card.Header padded>
                <H3>Personal Information</H3>
            </Card.Header>
            <Card.Footer padded>
                <YStack gap="$3">
                    <XStack gap="$4">
                        <YStack flex={1}>
                            <Text opacity={0.6}>Full Name</Text>
                            <Text>{familyMember.first_name} {familyMember.last_name}</Text>
                        </YStack>
                        <YStack flex={1}>
                            <Text opacity={0.6}>Gender</Text>
                            <Text>{familyMember.gender}</Text>
                        </YStack>
                    </XStack>
                    <XStack gap="$4">
                        <YStack flex={1}>
                            <Text opacity={0.6}>Birthday</Text>
                            <Text>{new Date(familyMember.birthday).toLocaleDateString()}</Text>
                        </YStack>
                        <YStack flex={1}>
                            <Text opacity={0.6}>Relation</Text>
                            <Text>{familyMember.relation_to_beneficiary}</Text>
                        </YStack>
                    </XStack>
                    <YStack>
                        <Text opacity={0.6}>Address</Text>
                        <Text>{familyMember.street_address}</Text>
                    </YStack>
                    <XStack gap="$4">
                        <YStack flex={1}>
                            <Text opacity={0.6}>Mobile</Text>
                            <Text>{familyMember.mobile}</Text>
                        </YStack>
                        <YStack flex={1}>
                            <Text opacity={0.6}>Landline</Text>
                            <Text>{familyMember.landline}</Text>
                        </YStack>
                    </XStack>
                    <XStack gap="$4">
                        <YStack flex={1}>
                            <Text opacity={0.6}>Email</Text>
                            <Text>{familyMember.email}</Text>
                        </YStack>
                        <YStack flex={1}>
                            <Text opacity={0.6}>Primary Caregiver</Text>
                            <Text>{familyMember.is_primary_caregiver ? "Yes" : "No"}</Text>
                        </YStack>
                    </XStack>
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default PersonalInformation;
