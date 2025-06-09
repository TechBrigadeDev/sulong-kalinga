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

const PersonalDetails = ({
    careWorker,
}: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>
                    Personal Details
                </SectionTitle>
            </Card.Header>
            <YStack>
                <YStack gap="$3">
                    <DetailRow
                        label="Educational Background"
                        value={
                            careWorker.educational_background
                        }
                    />
                    <DetailRow
                        label="Birthday"
                        value={new Date(
                            careWorker.birthday,
                        ).toLocaleDateString()}
                    />
                    <DetailRow
                        label="Gender"
                        value={careWorker.gender}
                    />
                    <DetailRow
                        label="Civil Status"
                        value={
                            careWorker.civil_status
                        }
                    />
                    <DetailRow
                        label="Religion"
                        value={
                            careWorker.religion
                        }
                    />
                    <DetailRow
                        label="Nationality"
                        value={
                            careWorker.nationality
                        }
                    />
                    <DetailRow
                        label="Volunteer Status"
                        value={
                            careWorker.volunteer_status
                        }
                    />
                    {/* <DetailRow
                        label="Municipality"
                        value={
                            careWorker
                                .municipality
                                ?.municipality_name ||
                            ""
                        }
                    /> */}
                </YStack>
            </YStack>
        </Card>
    );
};

export default PersonalDetails;
