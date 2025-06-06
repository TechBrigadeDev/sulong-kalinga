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

const GovernmentIDs = ({ careWorker }: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>
                    Government ID Numbers
                </SectionTitle>
            </Card.Header>
            <Card.Footer p="$4">
                <YStack gap="$3">
                    <DetailRow
                        label="SSS ID Number"
                        value={
                            careWorker.sss_id_number
                        }
                    />
                    <DetailRow
                        label="PhilHealth ID Number"
                        value={
                            careWorker.philhealth_id_number
                        }
                    />
                    <DetailRow
                        label="Pag-Ibig ID Number"
                        value={
                            careWorker.pagibig_id_number
                        }
                    />
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default GovernmentIDs;
