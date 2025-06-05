import AvatarImage from "components/Avatar";
import { useRouter } from "expo-router";
import { IFamilyMember } from "features/user-management/management.type";
import { Avatar, Button, H2, Text, XStack, YStack } from "tamagui";


interface Props {
    familyMember: IFamilyMember;
}

const FamilyMemberHeader = ({ familyMember }: Props) => {
    const router = useRouter();
    const fullName = `${familyMember.first_name} ${familyMember.last_name}`;
    const sinceDate = new Date(familyMember.beneficiary.created_at).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    });

    return (
        <YStack gap="$4" items="center">
            <Avatar size="$14" circular>
                <AvatarImage uri={familyMember.photo} fallback={familyMember.family_member_id.toLocaleString()}/>
            </Avatar>
            
            <YStack flex={1} gap="$2">
                <H2 size="$7">{fullName}</H2>
                <Text opacity={0.6}>Member since {sinceDate}</Text>
            </YStack>
            
            <Button
                size="$4"
                theme="light"
                onPress={() => router.push(`/(tabs)/options/user-management/family/${familyMember.family_member_id}/edit`)}
            >
                Edit
            </Button>
        </YStack>
    );
};

export default FamilyMemberHeader;
