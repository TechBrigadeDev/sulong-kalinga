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

const Documents = ({ careWorker }: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>
                    Documents
                </SectionTitle>
            </Card.Header>
            <YStack p="$4">
                <YStack gap="$3">
                    <DetailRow
                        label="Government Issued ID"
                        value={
                            careWorker.government_issued_id
                                ? "Available"
                                : "Not Available"
                        }
                    />
                    <DetailRow
                        label="Resume / CV"
                        value={
                            careWorker.cv_resume
                                ? "Available"
                                : "Not Available"
                        }
                    />
                </YStack>
            </YStack>
        </Card>
    );
};

export default Documents;
