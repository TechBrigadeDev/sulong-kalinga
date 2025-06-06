import { useState } from "react";
import { Button, Input, Label, XStack, YStack } from "tamagui";

const UpdatePassword = () => {
    const [currentPassword, setCurrentPassword] = useState("");
    const [newPassword, setNewPassword] = useState("");
    const [confirmPassword, setConfirmPassword] = useState("");
    const [showCurrent, setShowCurrent] = useState(false);
    const [showNew, setShowNew] = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);

    return (
        <YStack gap="$4" style={{ padding: 20 }}>
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
                {/* <Button theme="green" onPress={() => setIsOpen(false)}>
          Save Password
        </Button>
        <Button variant="outlined" onPress={() => setIsOpen(false)}>
          Cancel
        </Button> */}
            </XStack>
        </YStack>
    );
};

export default UpdatePassword;
