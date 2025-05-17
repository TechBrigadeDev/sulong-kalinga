import { useState } from "react";
import { Dialog, YStack, XStack, Input, Label, Button } from "tamagui";
import { updatePasswordStore } from "./store";

const UpdatePasswordDialog = () => {
  const {
    isOpen,
    setIsOpen
  } = updatePasswordStore();

  const [currentPassword, setCurrentPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [showCurrent, setShowCurrent] = useState(false);
  const [showNew, setShowNew] = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);

  return (
    <Dialog open={isOpen} onOpenChange={setIsOpen}>
      <Dialog.Portal>
        <Dialog.Overlay opacity={0.5} onPress={()=> setIsOpen(false)}/>
        <Dialog.Content bordered elevate width={400}>
          <YStack space="$4" style={{ padding: 20 }}>
            <Label htmlFor="current-password-update">Current Password</Label>
            <XStack style={{ alignItems: "center" }}>
              <Input
                id="current-password-update"
                placeholder="Enter current password"
                value={currentPassword}
                onChangeText={setCurrentPassword}
                secureTextEntry={!showCurrent}
                style={{ flex: 1 }}
              />
              <Button
                size="$2"
                variant="outlined"
                onPress={() => setShowCurrent((v) => !v)}
                aria-label={showCurrent ? "Hide password" : "Show password"}
              >
                {showCurrent ? "🙈" : "👁️"}
              </Button>
            </XStack>
            <Label htmlFor="new-password-update">New Password</Label>
            <XStack style={{ alignItems: "center" }}>
              <Input
                id="new-password-update"
                placeholder="Enter new password"
                value={newPassword}
                onChangeText={setNewPassword}
                secureTextEntry={!showNew}
                style={{ flex: 1 }}
              />
              <Button
                size="$2"
                variant="outlined"
                onPress={() => setShowNew((v) => !v)}
                aria-label={showNew ? "Hide password" : "Show password"}
              >
                {showNew ? "🙈" : "👁️"}
              </Button>
            </XStack>
            <Label htmlFor="confirm-password-update">Confirm Password</Label>
            <XStack style={{ alignItems: "center" }}>
              <Input
                id="confirm-password-update"
                placeholder="Confirm new password"
                value={confirmPassword}
                onChangeText={setConfirmPassword}
                secureTextEntry={!showConfirm}
                style={{ flex: 1 }}
              />
              <Button
                size="$2"
                variant="outlined"
                onPress={() => setShowConfirm((v) => !v)}
                aria-label={showConfirm ? "Hide password" : "Show password"}
              >
                {showConfirm ? "🙈" : "👁️"}
              </Button>
            </XStack>
            <XStack gap="$3" style={{ justifyContent: "flex-end", marginTop: 8 }}>
              <Button theme="green" onPress={() => setIsOpen(false)}>
                Save Password
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

export default UpdatePasswordDialog;