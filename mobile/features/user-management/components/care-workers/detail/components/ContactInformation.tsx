import DetailRow from "features/user-management/components/care-workers/detail/DetailRow";
import SectionTitle from "features/user-management/components/care-workers/detail/SectionTitle";
import { Card, YStack } from "tamagui";
import { type z } from "zod";

import { careWorkerSchema } from "~/features/user-management/schema/care-worker";

type ICareWorker = z.infer<
    typeof careWorkerSchema
>;

interface Props {
    careWorker: ICareWorker;
}

const ContactInformation = ({
    careWorker,
}: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>
                    Contact Information
                </SectionTitle>
            </Card.Header>
            <YStack>
                <YStack gap="$3">
                    <DetailRow
                        label="Email Address"
                        value={careWorker.email}
                    />
                    <DetailRow
                        label="Personal Email"
                        value={
                            careWorker.personal_email
                        }
                    />
                    <DetailRow
                        label="Mobile Number"
                        value={careWorker.mobile}
                    />
                    <DetailRow
                        label="Landline Number"
                        value={
                            careWorker.landline
                        }
                    />
                    <DetailRow
                        label="Current Address"
                        value={careWorker.address}
                    />
                </YStack>
            </YStack>
        </Card>
    );
};

export default ContactInformation;
