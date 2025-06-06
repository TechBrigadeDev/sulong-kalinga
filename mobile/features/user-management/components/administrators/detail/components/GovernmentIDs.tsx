import DetailRow from "features/user-management/components/administrators/detail/DetailRow";
import SectionTitle from "features/user-management/components/administrators/detail/SectionTitle";
import { Card, YStack } from "tamagui";
import { type z } from "zod";

import { adminSchema } from "~/features/user-management/schema/admin";

type IAdmin = z.infer<typeof adminSchema>;

interface Props {
    admin: IAdmin;
}

const GovernmentIDs = ({ admin }: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>Government ID Numbers</SectionTitle>
            </Card.Header>
            <Card.Footer p="$4">
                <YStack gap="$3">
                    <DetailRow label="SSS ID Number" value={admin.sss_id} />
                    <DetailRow label="PhilHealth ID Number" value={admin.philhealth_id} />
                    <DetailRow label="Pag-Ibig ID Number" value={admin.pagibig_id} />
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default GovernmentIDs;
