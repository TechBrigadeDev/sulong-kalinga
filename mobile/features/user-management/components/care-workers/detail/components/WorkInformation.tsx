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

const WorkInformation = ({
    careWorker,
}: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>
                    Work Information
                </SectionTitle>
            </Card.Header>
            <YStack>
                <YStack gap="$3">
                    <DetailRow
                        label="Status Period"
                        value={`${new Date(careWorker.status_start_date).toLocaleDateString()} - ${new Date(careWorker.status_end_date).toLocaleDateString()}`}
                    />
                    <DetailRow
                        label="Care Manager"
                        value={
                            careWorker.assigned_care_manager_id
                                ? `ID: ${careWorker.assigned_care_manager_id}`
                                : "Not Assigned"
                        }
                    />
                    <DetailRow
                        label="Organization Role"
                        value={
                            careWorker.organization_role_id
                                ? `ID: ${careWorker.organization_role_id}`
                                : "Not Assigned"
                        }
                    />
                </YStack>
            </YStack>
        </Card>
    );
};

export default WorkInformation;
