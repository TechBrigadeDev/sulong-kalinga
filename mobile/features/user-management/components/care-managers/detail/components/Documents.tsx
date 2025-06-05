import { Card, YStack } from "tamagui";
import { type z } from "zod";
import { careManagerSchema } from "~/features/user/management/schema/care-manager";
import DetailRow from "../DetailRow";
import SectionTitle from "../SectionTitle";

type ICareManager = z.infer<typeof careManagerSchema>;

interface Props {
    careManager: ICareManager;
}

const Documents = ({ careManager }: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>Documents</SectionTitle>
            </Card.Header>
            <Card.Footer p="$4">
                <YStack gap="$3">
                    <DetailRow label="Government Issued ID" value={careManager.government_issued_id ? "Available" : "Not Available"} />
                    <DetailRow label="Resume / CV" value={careManager.cv_resume ? "Available" : "Not Available"} />
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default Documents;
