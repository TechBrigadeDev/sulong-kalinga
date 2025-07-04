import DetailRow from "features/user-management/components/care-managers/detail/DetailRow";
import SectionTitle from "features/user-management/components/care-managers/detail/SectionTitle";
import { Card, YStack } from "tamagui";
import { type z } from "zod";

import { careManagerSchema } from "~/features/user-management/schema/care-manager";

type ICareManager = z.infer<
    typeof careManagerSchema
>;

interface Props {
    careManager: ICareManager;
}

const ContactInformation = ({
    careManager,
}: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>
                    Contact Information
                </SectionTitle>
            </Card.Header>
            <YStack p="$4">
                <YStack gap="$3">
                    <DetailRow
                        label="Email Address"
                        value={careManager.email}
                    />
                    <DetailRow
                        label="Personal Email"
                        value={
                            careManager.personal_email
                        }
                    />
                    <DetailRow
                        label="Mobile Number"
                        value={careManager.mobile}
                    />
                    <DetailRow
                        label="Landline Number"
                        value={
                            careManager.landline
                        }
                    />
                    <DetailRow
                        label="Current Address"
                        value={
                            careManager.address
                        }
                    />
                </YStack>
            </YStack>
        </Card>
    );
};

export default ContactInformation;
