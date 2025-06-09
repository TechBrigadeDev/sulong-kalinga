import { useForm } from "@tanstack/react-form";
import { log } from "common/debug";
import { useRouter } from "expo-router";
import { useUpdatePassword } from "features/user/user.hook";
import { updatePasswordSchema } from "features/user/user.schema";
import {
    Eye,
    EyeClosed,
} from "lucide-react-native";
import { useState } from "react";
import {
    Button,
    Input,
    Label,
    Spinner,
    Text,
    XStack,
    YStack,
} from "tamagui";

const UpdatePassword = () => {
    const router = useRouter();

    const [showCurrent, setShowCurrent] =
        useState(false);

    const [showNew, setShowNew] = useState(false);

    const [showConfirm, setShowConfirm] =
        useState(false);

    const {
        mutateAsync: updatePassword,
        isPending,
    } = useUpdatePassword({
        onSuccess: async () => {
            log("Password updated successfully");
            router.back();
        },
    });

    const form = useForm({
        defaultValues: {
            current_password: "",
            new_password: "",
            confirm_password: "",
        },
        validators: {
            onChange: updatePasswordSchema,
        },
        onSubmit: async (values) => {
            log(values.value);
            const validate =
                await updatePasswordSchema.safeParseAsync(
                    values.value,
                );
            if (!validate.success) {
                console.error(
                    "Validation failed:",
                    validate.error,
                );
                return;
            }
            await updatePassword(validate.data);
        },
    });

    return (
        <YStack gap="$4" style={{ padding: 20 }}>
            <Label htmlFor="current-password-update">
                Current Password
            </Label>
            <XStack
                style={{ alignItems: "center" }}
            >
                <form.Field name="current_password">
                    {(field) => (
                        <Input
                            id="current-password-update"
                            borderColor={
                                field.state.meta
                                    .errors
                                    .length > 0
                                    ? "red"
                                    : "$borderColor"
                            }
                            placeholder="Enter current password"
                            value={
                                field.state.value
                            }
                            onBlur={
                                field.handleBlur
                            }
                            onChangeText={(
                                value,
                            ) => {
                                field.handleChange(
                                    value,
                                );
                            }}
                            secureTextEntry={
                                !showCurrent
                            }
                            style={{ flex: 1 }}
                        />
                    )}
                </form.Field>

                <Button
                    size="$2"
                    variant="outlined"
                    onPress={() =>
                        setShowCurrent((v) => !v)
                    }
                    aria-label={
                        showCurrent
                            ? "Hide password"
                            : "Show password"
                    }
                >
                    {showCurrent ? (
                        <Eye size={16} />
                    ) : (
                        <EyeClosed size={16} />
                    )}
                </Button>
            </XStack>
            <Label htmlFor="new-password-update">
                New Password
            </Label>
            <XStack
                style={{ alignItems: "center" }}
            >
                <form.Field name="new_password">
                    {(field) => (
                        <Input
                            id="new-password-update"
                            borderColor={
                                field.state.meta
                                    .errors
                                    .length > 0
                                    ? "red"
                                    : "$borderColor"
                            }
                            placeholder="Enter new password"
                            value={
                                field.state.value
                            }
                            onBlur={
                                field.handleBlur
                            }
                            onChangeText={(
                                value,
                            ) => {
                                field.handleChange(
                                    value,
                                );
                            }}
                            secureTextEntry={
                                !showNew
                            }
                            style={{ flex: 1 }}
                        />
                    )}
                </form.Field>
                <Button
                    size="$2"
                    variant="outlined"
                    onPress={() =>
                        setShowNew((v) => !v)
                    }
                    aria-label={
                        showNew
                            ? "Hide password"
                            : "Show password"
                    }
                >
                    {showNew ? (
                        <Eye size={16} />
                    ) : (
                        <EyeClosed size={16} />
                    )}
                </Button>
            </XStack>
            <Label htmlFor="confirm-password-update">
                Confirm Password
            </Label>
            <XStack
                style={{ alignItems: "center" }}
            >
                <form.Field name="confirm_password">
                    {(field) => (
                        <Input
                            id="confirm-password-update"
                            borderColor={
                                field.state.meta
                                    .errors
                                    .length > 0
                                    ? "red"
                                    : "$borderColor"
                            }
                            placeholder="Confirm new password"
                            value={
                                field.state.value
                            }
                            onBlur={
                                field.handleBlur
                            }
                            onChangeText={(
                                value,
                            ) => {
                                field.handleChange(
                                    value,
                                );
                            }}
                            secureTextEntry={
                                !showConfirm
                            }
                            style={{ flex: 1 }}
                        />
                    )}
                </form.Field>
                <Button
                    size="$2"
                    variant="outlined"
                    onPress={() =>
                        setShowConfirm((v) => !v)
                    }
                    aria-label={
                        showConfirm
                            ? "Hide password"
                            : "Show password"
                    }
                >
                    {showConfirm ? (
                        <Eye size={16} />
                    ) : (
                        <EyeClosed size={16} />
                    )}
                </Button>
            </XStack>
            <Text fontSize={13} color="#64748b">
                For security, please enter your
                current password to confirm this
                change. New password must be at
                least 8 characters long and
                different from your current
                password.
            </Text>
            <XStack
                display="flex"
                style={{ marginTop: 8 }}
            >
                <Button
                    theme="dark_green"
                    onPress={() =>
                        form.handleSubmit()
                    }
                >
                    {isPending && (
                        <Spinner size="small" />
                    )}
                    Save Password
                </Button>
            </XStack>
        </YStack>
    );
};

export default UpdatePassword;
