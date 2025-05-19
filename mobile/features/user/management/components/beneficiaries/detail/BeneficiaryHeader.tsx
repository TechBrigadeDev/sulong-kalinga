import { Avatar, H2, Text, XStack, YStack } from "tamagui";
import { IBeneficiary } from "~/user.schema";

interface Props {
    beneficiary: IBeneficiary;
}

const BeneficiaryHeader = ({ beneficiary }: Props) => {
    const fullName = `${beneficiary.first_name} ${beneficiary.last_name}`;
    const sinceDate = new Date(beneficiary.created_at).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    });

    return (
        <XStack m="$4">
            {/* <Avatar size="$12" circular>
                <Avatar.Image src={beneficiary.photo || undefined} />
                <Avatar.Fallback bg="gray">
                    {fullName.split(' ').map(n => n[0]).join('')}
                </Avatar.Fallback>
            </Avatar> */}
            <YStack ml="$4">
                <H2>{fullName}</H2>
                <Text opacity={0.6}>A Beneficiary since {sinceDate}</Text>
            </YStack>
        </XStack>
    );
};

export default BeneficiaryHeader;
