import { IFamilyMember } from "features/user/management/management.type";
import { Card, H3, Text, XStack, YStack } from "tamagui";


interface Props {
    familyMember: IFamilyMember;
}

const InfoItem = ({ label, value }: { label: string; value: string | number | boolean | null }) => (
    <YStack gap="$1">
        <Text opacity={0.6} fontSize="$3">{label}</Text>
        <Text fontSize="$4">
            {typeof value === 'boolean' ? (value ? 'Yes' : 'No') : value || 'Not provided'}
        </Text>
    </YStack>
);

const PersonalInformation = ({ familyMember }: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <H3 size="$6">Personal Information</H3>
            </Card.Header>
            <Card.Footer p="$4">
                <YStack gap="$4">
                    <InfoItem 
                        label="Full Name" 
                        value={`${familyMember.first_name} ${familyMember.last_name}`} 
                    />
                    <InfoItem 
                        label="Contact Information" 
                        value={`${familyMember.email}\n${familyMember.mobile}`} 
                    />
                    <XStack gap="$4">
                        <YStack flex={1} gap="$3">
                            <InfoItem 
                                label="Relation to Beneficiary" 
                                value={familyMember.relation_to_beneficiary} 
                            />
                        </YStack>
                        <YStack flex={1} gap="$3">
                            <InfoItem 
                                label="Primary Caregiver" 
                                value={familyMember.is_primary_caregiver} 
                            />
                        </YStack>
                    </XStack>
                    <InfoItem 
                        label="Connected Beneficiary" 
                        value={`${familyMember.beneficiary.first_name} ${familyMember.beneficiary.last_name}`} 
                    />
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default PersonalInformation;
