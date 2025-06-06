import DetailRow from "features/user-management/components/care-managers/detail/DetailRow";
import SectionTitle from "features/user-management/components/care-managers/detail/SectionTitle";
import { Card, YStack } from "tamagui";
import { type z } from "zod";

import { careManagerSchema } from "~/features/user-management/schema/care-manager";

type ICareManager = z.infer<typeof careManagerSchema>;

interface Props {
    careManager: ICareManager;
}

const PersonalDetails = ({ careManager }: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>Personal Details</SectionTitle>
            </Card.Header>
            <Card.Footer p="$4">
                <YStack gap="$3">
                    <DetailRow
                        label="Educational Background"
                        value={careManager.educational_background}
                    />
                    <DetailRow
                        label="Birthday"
                        value={new Date(careManager.birthday).toLocaleDateString()}
                    />
                    <DetailRow label="Gender" value={careManager.gender} />
                    <DetailRow label="Civil Status" value={careManager.civil_status} />
                    <DetailRow label="Religion" value={careManager.religion} />
                    <DetailRow label="Nationality" value={careManager.nationality} />
                    <DetailRow
                        label="Assigned Municipality"
                        value={careManager.municipality.municipality_name}
                    />
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default PersonalDetails;
