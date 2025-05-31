import React from 'react';
import { ScrollView } from 'react-native';
import {
  Avatar,
  Button,
  Card,
  H4,
  Paragraph,
  XStack,
  YStack,
  Text,
  View,
  Sheet,
  Image,
  Stack,
} from 'tamagui';
import { ChevronLeft, Edit2, Trash } from 'lucide-react-native';
import { useRouter } from 'expo-router';
import { ICareManager } from '../../../management.type';

type CareManagerDetailProps = {
    careManager: ICareManager
};

const DetailRow = ({ label, value }: { label: string; value: string | number }) => (
  <XStack gap="$3">
    <Text flex={1}>{label}:</Text>
    <Text flex={2}>{value}</Text>
  </XStack>
);

const Section = ({ title, children }: { title: string; children: React.ReactNode }) => (
  <Card size="$4" p="$4">
    <H4>{title}</H4>
    <YStack gap="$2">
      {children}
    </YStack>
  </Card>
);

function CareManagerDetail({ careManager }: CareManagerDetailProps) {
  const router = useRouter();

  if (!careManager) {
    return (
      <View style={{ padding: 16, alignItems: 'center', justifyContent: 'center' }}>
        <Text>Care Manager data is not available</Text>
      </View>
    );
  }

  return (
    <ScrollView>
      <YStack p="$4" gap="$4">
        {/* Profile Header */}
        <Card size="$4" p="$4">
          <XStack gap="$4">
            <Avatar circular size="$12">
              <Avatar.Image source={{ uri: careManager.photo_url || 'https://placehold.co/200' }} />
              <Avatar.Fallback backgroundColor="gray" />
            </Avatar>
            <YStack gap="$2">
              <H4>{careManager.first_name} {careManager.last_name}</H4>
              <Paragraph theme="alt2">A Care Manager since {new Date(careManager.created_at).toLocaleDateString()}</Paragraph>
              <Button size="$3" chromeless theme={careManager.status === 'Active' ? 'green' : 'red'}>
                {careManager.status} Care Manager
              </Button>
            </YStack>
          </XStack>
        </Card>

        {/* Personal Details */}
        <Section title="Personal Details">
          <DetailRow label="Educational Background" value={careManager.educational_background} />
          <DetailRow label="Birthday" value={new Date(careManager.birthday).toLocaleDateString()} />
          <DetailRow label="Gender" value={careManager.gender} />
          <DetailRow label="Civil Status" value={careManager.civil_status} />
          <DetailRow label="Religion" value={careManager.religion} />
          <DetailRow label="Nationality" value={careManager.nationality} />
          <DetailRow label="Assigned Municipality" value={careManager.municipality.municipality_name} />
          <DetailRow label="Email Address" value={careManager.email} />
          <DetailRow label="Mobile Number" value={careManager.mobile} />
          <DetailRow label="Landline Number" value={careManager.landline} />
          <DetailRow label="Current Address" value={careManager.address} />
        </Section>

        {/* Documents */}
        <Section title="Documents">
          <DetailRow label="Government Issued ID" value={careManager.government_issued_id_url ? "Available" : "N/A"} />
          <DetailRow label="Resume / CV" value={careManager.cv_resume_url ? "Available" : "N/A"} />
        </Section>

        {/* Government ID Numbers */}
        <Section title="Government ID Numbers">
          <DetailRow label="SSS ID Number" value={careManager.sss_id_number} />
          <DetailRow label="PhilHealth ID Number" value={careManager.philhealth_id_number} />
          <DetailRow label="Pag-Ibig ID Number" value={careManager.pagibig_id_number} />
        </Section>
      </YStack>
    </ScrollView>
  );
}
export default CareManagerDetail;