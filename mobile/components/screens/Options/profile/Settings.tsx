import { YStack, XStack, Card, Text, Button, Separator } from 'tamagui';
import { useUser } from '~/features/user/user.hook';
import Badge from '~/components/Bagde';
import { updateEmailStore } from '~/features/user/components/UpdateEmail/store';
import { updatePasswordStore } from '~/features/user/components/UpdatePassword/store';

const ProfileSettings = () => {
  const {
    setIsOpen: setUpdateEmailOpen,
  } = updateEmailStore();
  const {
    setIsOpen: setUpdatePasswordOpen,
  } = updatePasswordStore();

  const handleUpdateEmail = () => {
    setUpdateEmailOpen(true);
  };
  const handleUpdatePassword = () => {
    setUpdatePasswordOpen(true);
  };

  const { data: userData } = useUser();

  return (
    <YStack style={{ flex: 1, backgroundColor: 'var(--background)' }}>
      <Card style={{ padding: 24, marginTop: 24 }}>
        <YStack gap="$3">
          <XStack gap="$2">
            <Text fontWeight="bold">Email:</Text>
            <Text>{userData?.email}</Text>
          </XStack>
          <XStack gap="$2" style={{ alignItems: 'center' }}>
            <Text fontWeight="bold">Account Status:</Text>
            <Text style={{ background: '#22c55e', color: 'white', padding: '2px 8px', borderRadius: 4, fontSize: 13 }}>
              <Badge variant={userData?.status === 'Active' ? 'success' : 'warning'}>
                {userData?.status}
              </Badge>
            </Text>
          </XStack>
          <XStack gap="$2">
            <Text fontWeight="bold">SSS ID Number:</Text>
            <Text>1234567890</Text>
          </XStack>
          <XStack gap="$2">
            <Text fontWeight="bold">PhilHealth ID Number:</Text>
            <Text>123456789011</Text>
          </XStack>
          <XStack gap="$2">
            <Text fontWeight="bold">Pag-IBIG ID Number:</Text>
            <Text>123456789000</Text>
          </XStack>
        </YStack>
        <XStack gap="$3" style={{ marginTop: 32, justifyContent: 'flex-end' }}>
          <Button theme="blue" onPress={handleUpdateEmail}>Update Email</Button>
          <Button theme="blue" onPress={handleUpdatePassword}>Update Password</Button>
        </XStack>
      </Card>
    </YStack>
  );
};

export default ProfileSettings;