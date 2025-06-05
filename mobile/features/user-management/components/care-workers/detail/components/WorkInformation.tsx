import { Card, YStack } from "tamagui";
import { type z } from "zod";
import { careWorkerSchema } from "~/features/user-management/schema/care-worker";
import DetailRow from "../DetailRow";
import SectionTitle from "../SectionTitle";

type ICareWorker = z.infer<typeof careWorkerSchema>;

interface Props {
    careWorker: ICareWorker;
}

const WorkInformation = ({ careWorker }: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>Work Information</SectionTitle>
            </Card.Header>
            <Card.Footer p="$4">
                <YStack gap="$3">
                    <DetailRow 
                        label="Status Period" 
                        value={`${new Date(careWorker.status_start_date).toLocaleDateString()} - ${new Date(careWorker.status_end_date).toLocaleDateString()}`} 
                    />
                    <DetailRow 
                        label="Care Manager" 
                        value={careWorker.assigned_care_manager_id ? `ID: ${careWorker.assigned_care_manager_id}` : 'Not Assigned'} 
                    />
                    <DetailRow 
                        label="Organization Role" 
                        value={careWorker.organization_role_id ? `ID: ${careWorker.organization_role_id}` : 'Not Assigned'} 
                    />
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default WorkInformation;
