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

const GovernmentIDs = ({
    careManager,
}: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>
                    Government ID Numbers
                </SectionTitle>
            </Card.Header>
            <YStack p="$4">
                <YStack gap="$3">
                    <DetailRow
                        label="SSS ID Number"
                        value={
                            careManager.sss_id_number
                        }
                    />
                    <DetailRow
                        label="PhilHealth ID Number"
                        value={
                            careManager.philhealth_id_number
                        }
                    />
                    <DetailRow
                        label="Pag-Ibig ID Number"
                        value={
                            careManager.pagibig_id_number
                        }
                    />
                </YStack>
            </YStack>
        </Card>
    );
};

export default GovernmentIDs;
