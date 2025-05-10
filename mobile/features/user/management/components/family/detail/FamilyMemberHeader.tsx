import { Avatar, Button, H2, Text, XStack, YStack } from "tamagui";
import { IFamilyMember } from "../../../../../user.schema";
import { useRouter } from "expo-router";

interface Props {
    familyMember: IFamilyMember;
}

const FamilyMemberHeader = ({ familyMember }: Props) => {
    const router = useRouter();
    const fullName = `${familyMember.first_name} ${familyMember.last_name}`;
    const sinceDate = new Date(familyMember.created_at).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    });

    return (
        <XStack m="$4">
            <XStack flex={1}>
                <Avatar size="$12" circular>
                    <Avatar.Image src={familyMember.photo || undefined} />
                    <Avatar.Fallback bg="gray">
                        {fullName.split(' ').map(n => n[0]).join('')}
                    </Avatar.Fallback>
                </Avatar>
                <YStack ml="$4" flex={1}>
                    <H2>{fullName}</H2>
                    <Text opacity={0.6}>Since {sinceDate}</Text>
                </YStack>
                <Button
                    size="$3"
                    theme="light"
                    borderColor="gray"
                    onPress={() => router.push(`/user-management/family/${familyMember.family_member_id}/edit`)}
                    variant="outlined"
                >
                    Edit
                </Button>
            </XStack>
        </XStack>
    );
};

export default FamilyMemberHeader;
