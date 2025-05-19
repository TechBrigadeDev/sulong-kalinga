import { useState } from "react";
import { Dialog, YStack, XStack, Input, Label, Button, Text } from "tamagui";
import { updateEmailStore } from "./store";

const UpdateEmailDialog = () => {
  const {
    isOpen,
    setIsOpen,
  } = updateEmailStore();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);

  return (
    <Dialog open={isOpen} onOpenChange={setIsOpen}>
      <Dialog.Portal>
        <Dialog.Overlay opacity={0.5} onPress={()=> setIsOpen(false)}/>
        <Dialog.Content bordered elevate width={400}>
          <YStack space="$4" style={{ padding: 20 }}>
            <Label htmlFor="new-email-update">New Email</Label>
            <Input
              id="new-email-update"
              placeholder="Enter new email address"
              value={email}
              onChangeText={setEmail}
              autoCapitalize="none"
              keyboardType="email-address"
            />
            <Label htmlFor="current-password-update">Current Password</Label>
            <XStack style={{ alignItems: "center" }}>
              <Input
                id="current-password-update"
                placeholder="Enter current password"
                value={password}
                onChangeText={setPassword}
                secureTextEntry={!showPassword}
                style={{ flex: 1 }}
              />
              <Button
                size="$2"
                variant="outlined"
                onPress={() => setShowPassword((v) => !v)}
                aria-label={showPassword ? "Hide password" : "Show password"}
              >
                {showPassword ? "🙈" : "👁️"}
              </Button>
            </XStack>
            <Text fontSize={13} color="#64748b">
              For security, please enter your current password to confirm this change.
            </Text>
            <XStack gap="$3" style={{ justifyContent: "flex-end", marginTop: 8 }}>
              <Button theme="green" onPress={() => setIsOpen(false)}>
                Save Email
              </Button>
              <Button variant="outlined" onPress={() => setIsOpen(false)}>
                Cancel
              </Button>
            </XStack>
          </YStack>
        </Dialog.Content>
      </Dialog.Portal>
    </Dialog>
  );
};

export default UpdateEmailDialog;