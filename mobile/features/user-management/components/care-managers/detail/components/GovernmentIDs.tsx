import { Card, YStack } from "tamagui";
import { type z } from "zod";
import { careManagerSchema } from "~/features/user/management/schema/care-manager";
import DetailRow from "../DetailRow";
import SectionTitle from "../SectionTitle";

type ICareManager = z.infer<typeof careManagerSchema>;

interface Props {
    careManager: ICareManager;
}

const GovernmentIDs = ({ careManager }: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>Government ID Numbers</SectionTitle>
            </Card.Header>
            <Card.Footer p="$4">
                <YStack gap="$3">
                    <DetailRow label="SSS ID Number" value={careManager.sss_id_number} />
                    <DetailRow label="PhilHealth ID Number" value={careManager.philhealth_id_number} />
                    <DetailRow label="Pag-Ibig ID Number" value={careManager.pagibig_id_number} />
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default GovernmentIDs;
