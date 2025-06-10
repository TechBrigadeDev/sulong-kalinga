import {
    Avatar,
    Card,
    H4,
    Paragraph,
    XStack,
    YStack,
} from "tamagui";
import { type z } from "zod";

import AvatarImage from "~/components/Avatar";
import Badge from "~/components/Bagde";
import { careManagerSchema } from "~/features/user-management/schema/care-manager";

type ICareManager = z.infer<
    typeof careManagerSchema
>;

interface Props {
    careManager: ICareManager;
}

const CareManagerHeader = ({
    careManager,
}: Props) => {
    const joinDate = new Date(
        careManager.created_at,
    );

    return (
        <Card
            elevate
            backgroundColor="$background"
        >
            <Card.Header p="$4">
                <XStack gap="$4">
                    <Avatar circular size="$12">
                        <AvatarImage
                            uri={
                                careManager.photo_url
                            }
                            fallback={careManager.id.toString()}
                        />
                    </Avatar>
                    <YStack
                        gap="$2"
                        p="$4"
                        flex={1}
                    >
                        <H4>
                            {
                                careManager.first_name
                            }{" "}
                            {
                                careManager.last_name
                            }
                        </H4>
                        <Paragraph opacity={0.7}>
                            A Care Manager since{" "}
                            {joinDate.toLocaleDateString(
                                "en-US",
                                {
                                    month: "long",
                                    day: "numeric",
                                    year: "numeric",
                                },
                            )}
                        </Paragraph>
                        <XStack>
                            <Badge
                                variant={
                                    careManager.status.toLowerCase() ===
                                    "active"
                                        ? "success"
                                        : "warning"
                                }
                            >
                                {
                                    careManager.status
                                }{" "}
                                Care Manager
                            </Badge>
                        </XStack>
                    </YStack>
                </XStack>
            </Card.Header>
        </Card>
    );
};

export default CareManagerHeader;
