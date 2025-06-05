import { Avatar, Card, H4, Paragraph, XStack, YStack } from "tamagui";
import { type z } from "zod";
import Badge from "~/components/Bagde";
import { careWorkerSchema } from "~/features/user/management/schema/care-worker";
import SectionTitle from "../SectionTitle";
import AvatarImage from "../../../../../../../components/Avatar";

type ICareWorker = z.infer<typeof careWorkerSchema>;

interface Props {
    careWorker: ICareWorker;
}

const CareWorkerHeader = ({ careWorker }: Props) => {
    const joinDate = new Date(careWorker.created_at);
    const status = careWorker.status || 'Active';

    return (
        <Card elevate>
            <Card.Header p="$4">
                <XStack gap="$4">
                    <Avatar circular size="$12">
                        <AvatarImage
                            uri={careWorker.photo_url || ''}
                            fallback={careWorker.id.toString()}
                        />
                    </Avatar>
                    <YStack flex={1} gap="$2">
                        <H4>{careWorker.first_name} {careWorker.last_name}</H4>
                        <Paragraph>
                            A Care Worker since {joinDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}
                        </Paragraph>
                        <Badge 
                            variant={status.toLowerCase() === 'active' ? 'success' : 'warning'}
                        >
                            {status}
                        </Badge>
                    </YStack>
                </XStack>
            </Card.Header>
        </Card>
    );
};

export default CareWorkerHeader;
