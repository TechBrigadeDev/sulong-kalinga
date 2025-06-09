import {
    Card,
    H6,
    Paragraph,
    XStack,
    YStack,
} from "tamagui";

interface Props {
    beneficiaryConfirmed: boolean;
    familyConfirmed: boolean;
    confirmedOn?: string;
}

const StatusCard = ({
    beneficiaryConfirmed,
    familyConfirmed,
    confirmedOn,
}: Props) => {
    return (
        <Card bordered p="$4" mb="$2">
            <YStack space="$4">
                <H6>Care Plan Status</H6>
                <YStack space="$2">
                    <XStack justify="space-between">
                        <Paragraph>
                            Beneficiary:
                        </Paragraph>
                        <Paragraph>
                            {beneficiaryConfirmed
                                ? "Confirmed"
                                : "Not Confirmed"}
                        </Paragraph>
                    </XStack>
                    <XStack justify="space-between">
                        <Paragraph>
                            Family:
                        </Paragraph>
                        <Paragraph>
                            {familyConfirmed
                                ? "Confirmed"
                                : "Not Confirmed"}
                        </Paragraph>
                    </XStack>
                    {confirmedOn && (
                        <XStack justify="space-between">
                            <Paragraph>
                                Confirmed On:
                            </Paragraph>
                            <Paragraph>
                                {confirmedOn}
                            </Paragraph>
                        </XStack>
                    )}
                </YStack>
            </YStack>
        </Card>
    );
};

export default StatusCard;
