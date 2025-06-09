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

const Documents = ({ careManager }: Props) => {
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
                            careManager.government_issued_id
                                ? "Available"
                                : "Not Available"
                        }
                    />
                    <DetailRow
                        label="Resume / CV"
                        value={
                            careManager.cv_resume
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
