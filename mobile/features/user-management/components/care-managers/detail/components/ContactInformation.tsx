import { Card, YStack } from "tamagui";
import { type z } from "zod";
import { careManagerSchema } from "~/features/user-management/schema/care-manager";
import DetailRow from "../DetailRow";
import SectionTitle from "../SectionTitle";

type ICareManager = z.infer<typeof careManagerSchema>;

interface Props {
    careManager: ICareManager;
}

const ContactInformation = ({ careManager }: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>Contact Information</SectionTitle>
            </Card.Header>
            <Card.Footer p="$4">
                <YStack gap="$3">
                    <DetailRow label="Email Address" value={careManager.email} />
                    <DetailRow label="Personal Email" value={careManager.personal_email} />
                    <DetailRow label="Mobile Number" value={careManager.mobile} />
                    <DetailRow label="Landline Number" value={careManager.landline} />
                    <DetailRow label="Current Address" value={careManager.address} />
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default ContactInformation;
