import AvatarImage from "components/Avatar";
import {
    Avatar,
    H2,
    Text,
    XStack,
    YStack,
} from "tamagui";

import { IBeneficiary } from "~/features/user-management/management.type";

interface Props {
    beneficiary: IBeneficiary;
}

const BeneficiaryHeader = ({
    beneficiary,
}: Props) => {
    const fullName = `${beneficiary.first_name} ${beneficiary.last_name}`;
    const sinceDate = new Date(
        beneficiary.created_at,
    ).toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
    });

    return (
        <XStack
            display="flex"
            flexDirection="column"
            items="center"
            marginBlock="$4"
        >
            <Avatar size="$12" circular>
                <AvatarImage
                    uri={beneficiary.photo}
                    fallback={beneficiary.beneficiary_id.toString()}
                />
            </Avatar>
            <YStack ml="$4" items="center">
                <H2>{fullName}</H2>
                <Text opacity={0.6}>
                    A Beneficiary since{" "}
                    {sinceDate}
                </Text>
            </YStack>
        </XStack>
    );
};

export default BeneficiaryHeader;
