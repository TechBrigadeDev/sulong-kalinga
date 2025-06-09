import DetailRow from "features/user-management/components/administrators/detail/DetailRow";
import SectionTitle from "features/user-management/components/administrators/detail/SectionTitle";
import { Card, YStack } from "tamagui";
import { type z } from "zod";

import { adminSchema } from "~/features/user-management/schema/admin";

type IAdmin = z.infer<typeof adminSchema>;

interface Props {
    admin: IAdmin;
}

const Documents = ({ admin }: Props) => {
    return (
        <Card elevate>
            <Card.Header p="$4">
                <SectionTitle>
                    Documents
                </SectionTitle>
            </Card.Header>
            <YStack>
                <YStack gap="$3">
                    <DetailRow
                        label="Government Issued ID"
                        value={
                            admin.photo
                                ? "Available"
                                : "Not Available"
                        }
                    />
                    <DetailRow
                        label="Resume / CV"
                        value={"Not Available"}
                    />
                </YStack>
            </YStack>
        </Card>
    );
};

export default Documents;
