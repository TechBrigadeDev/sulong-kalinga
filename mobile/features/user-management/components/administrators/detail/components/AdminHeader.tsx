import AvatarImage from "components/Avatar";
import { Avatar, Card, H4, Paragraph, Text, XStack, YStack } from "tamagui";
import { type z } from "zod";

import Badge from "~/components/Bagde";
import { adminSchema } from "~/features/user-management/schema/admin";

type IAdmin = z.infer<typeof adminSchema>;

interface Props {
    admin: IAdmin;
}

const AdminHeader = ({ admin }: Props) => {
    const joinDate = new Date(admin.created_at);
    const status = admin.status || 'Active';

    return (
        <Card elevate>
            <Card.Header p="$4">
                <XStack gap="$4">
                    <Avatar circular size="$12">
                        <AvatarImage
                            uri={admin.photo_url}
                            fallback={admin.id.toString()}
                        />
                    </Avatar>
                    <YStack flex={1} gap="$2">
                        <H4>{admin.first_name} {admin.last_name}</H4>
                        <Paragraph>
                            A Project Coordinator since {joinDate.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}
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

export default AdminHeader;
