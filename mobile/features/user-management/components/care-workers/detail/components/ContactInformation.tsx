import { Card, YStack } from "tamagui";
import { type z } from "zod";
import { careWorkerSchema } from "~/features/user-management/schema/care-worker";
import DetailRow from "../DetailRow";
import SectionTitle from "../SectionTitle";

type ICareWorker = z.infer<typeof careWorkerSchema>;

interface Props {
    careWorker: ICareWorker;
}

const ContactInformation = ({ careWorker }: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>Contact Information</SectionTitle>
            </Card.Header>
            <Card.Footer p="$4">
                <YStack gap="$3">
                    <DetailRow label="Email Address" value={careWorker.email} />
                    <DetailRow label="Personal Email" value={careWorker.personal_email} />
                    <DetailRow label="Mobile Number" value={careWorker.mobile} />
                    <DetailRow label="Landline Number" value={careWorker.landline} />
                    <DetailRow label="Current Address" value={careWorker.address} />
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default ContactInformation;
