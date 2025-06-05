import { Card, YStack } from "tamagui";
import { type z } from "zod";
import { adminSchema } from "~/features/user-management/schema/admin";
import DetailRow from "../DetailRow";
import SectionTitle from "../SectionTitle";

type IAdmin = z.infer<typeof adminSchema>;

interface Props {
    admin: IAdmin;
}

const PersonalDetails = ({ admin }: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>Personal Details</SectionTitle>
            </Card.Header>
            <Card.Footer p="$4">
                <YStack gap="$3">
                    <DetailRow label="Educational Background" value={admin.educational_background} />
                    <DetailRow label="Birthday" value={admin.birthday} />
                    <DetailRow label="Gender" value={admin.gender} />
                    <DetailRow label="Civil Status" value={admin.civil_status} />
                    <DetailRow label="Religion" value={admin.religion} />
                    <DetailRow label="Nationality" value={admin.nationality} />
                </YStack>
            </Card.Footer>
        </Card>
    );
};

export default PersonalDetails;
