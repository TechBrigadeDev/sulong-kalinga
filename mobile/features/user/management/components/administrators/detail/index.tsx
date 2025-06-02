import { Avatar, Card, H4, Paragraph, ScrollView, Text,XStack, YStack } from "tamagui";
import { type z } from "zod";

import Badge from "~/components/Bagde";
import { adminSchema } from "~/features/user/management/schema/admin";

type IAdmin = z.infer<typeof adminSchema>;

interface AdminDetailProps {
    admin: IAdmin;
}

const DetailRow = ({ label, value }: { label: string; value: string | null }) => (
    <XStack margin="$2">
        <Text opacity={0.6} flex={1} fontSize="$4">{label}:</Text>
        <Text flex={2} fontSize="$4">{value || 'N/A'}</Text>
    </XStack>
);

const SectionTitle = ({ children }: { children: React.ReactNode }) => (
    <H4>{children}</H4>
);

function AdminDetail({ admin }: AdminDetailProps) {
    if (!admin) {
        return (
            <YStack p="$4" flex={1}>
                <Text textAlign="center">Administrator data is not available</Text>
            </YStack>
        );
    }

    const joinDate = new Date(admin.created_at);
    const status = admin?.status || 'Active';

    return (
        <ScrollView>
            <YStack p="$4" space="$4">
                {/* Profile Header */}
                <Card elevate>
                    <Card.Header p="$4">
                        <XStack gap="$4">
                            <Avatar circular size="$12">
                                <Avatar.Image source={{ uri: admin.photo_url || 'https://placehold.co/200' }} />
                                <Avatar.Fallback backgroundColor="gray" />
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

                {/* Personal Details */}
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

                {/* Contact Information */}
                <Card elevate>
                    <Card.Header p="$4">
                        <SectionTitle>Contact Information</SectionTitle>
                    </Card.Header>
                    <Card.Footer p="$4">
                        <YStack gap="$3">
                            <DetailRow label="Email Address" value={admin.email} />
                            <DetailRow label="Mobile Number" value={admin.mobile} />
                            <DetailRow label="Landline Number" value={admin?.landline} />
                            <DetailRow label="Current Address" value={admin?.address} />
                        </YStack>
                    </Card.Footer>
                </Card>

                {/* Documents */}
                <Card elevate>
                    <Card.Header p="$4">
                        <SectionTitle>Documents</SectionTitle>
                    </Card.Header>
                    <Card.Footer p="$4">
                        <YStack gap="$3">
                            <DetailRow label="Government Issued ID" value={admin.photo ? "Available" : "Not Available"} />
                            <DetailRow label="Resume / CV" value={"Not Available"} />
                        </YStack>
                    </Card.Footer>
                </Card>

                {/* Government ID Numbers */}
                <Card elevate>
                    <Card.Header p="$4">
                        <SectionTitle>Government ID Numbers</SectionTitle>
                    </Card.Header>
                    <Card.Footer p="$4">
                        <YStack gap="$3">
                            <DetailRow label="SSS ID Number" value={admin?.sss_id} />
                            <DetailRow label="PhilHealth ID Number" value={admin?.philhealth_id} />
                            <DetailRow label="Pag-Ibig ID Number" value={admin?.pagibig_id} />
                        </YStack>
                    </Card.Footer>
                </Card>
            </YStack>
        </ScrollView>
    );
}

export default AdminDetail;
