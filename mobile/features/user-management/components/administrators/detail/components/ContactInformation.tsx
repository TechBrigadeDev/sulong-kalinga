import DetailRow from "features/user-management/components/administrators/detail/DetailRow";
import SectionTitle from "features/user-management/components/administrators/detail/SectionTitle";
import { Card, YStack } from "tamagui";
import { type z } from "zod";

import { adminSchema } from "~/features/user-management/schema/admin";

type IAdmin = z.infer<typeof adminSchema>;

interface Props {
    admin: IAdmin;
}

const ContactInformation = ({ admin }: Props) => {
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
                        value={admin.email}
                    />
                    <DetailRow
                        label="Mobile Number"
                        value={admin.mobile}
                    />
                    <DetailRow
                        label="Landline Number"
                        value={admin.landline}
                    />
                    <DetailRow
                        label="Current Address"
                        value={admin.address}
                    />
                </YStack>
            </YStack>
        </Card>
    );
};

export default ContactInformation;
