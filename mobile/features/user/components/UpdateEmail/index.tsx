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
import {
    useForm
} from "@tanstack/react-form";
import { updateEmailSchema } from "../../user.schema";
import { Eye, EyeClosed } from "lucide-react-native";
import { useUpdateEmail } from "../../user.hook";
import { log } from "../../../../common/debug";
import { useRouter } from "expo-router";

const UpdateEmail = () => {
    const router = useRouter();
    const [showPassword, setShowPassword] =
        useState(false);

    const {
        mutateAsync: updateEmail,
        isPending,
    } = useUpdateEmail({
        onSuccess: async () => {
            log("Email updated successfully");
            router.push("/(tabs)/options/profile");
        },

    });

    const form = useForm({
        defaultValues: {
            new_email: "",
            password: ""
        },
        validators: {
            onChange: updateEmailSchema
        },
        onSubmit: async (values) => {
            log(
                values.value,
            )
            const validate = await updateEmailSchema.safeParseAsync(values.value);
            if (!validate.success) {
                console.error(
                    "Validation failed:",
                    validate.error,
                );
                return;
            }
            await updateEmail(validate.data);
        }
    })

    return (
        <YStack gap="$4" style={{ padding: 20 }}>
            <Label htmlFor="new-email-update">
                New Email
            </Label>
            <form.Field
                name="new_email"
                children={
                    (field) => (
                        <Input
                            id="new-email-update"
                            borderColor={field.state.meta.errors.length > 0 ? "red" : "$borderColor"}
                            placeholder="Enter new email address"
                            value={field.state.value}
                            onBlur={field.handleBlur}
                            onChangeText={(value) => {
                                field.handleChange(value);
                            }}
                            autoCapitalize="none"
                            keyboardType="email-address"
                        />
                    )}
            />
            <Label htmlFor="current-password-update">
                Current Password
            </Label>
            <XStack
                style={{ alignItems: "center" }}
            >
                <form.Field
                    name="password"
                    children={
                        (field) => (
                    <Input
                        id="current-password-update"
                        borderColor={field.state.meta.errors.length > 0 ? "red" : "$borderColor"}
                        placeholder="Enter current password"
                        value={field.state.value}
                        onBlur={field.handleBlur}
                        onChangeText={(value) => {
                            field.handleChange(value);
                        }}
                        secureTextEntry={
                            !showPassword
                        }
                        style={{ flex: 1 }}
                    />
                    )}
                />
                <Button
                    size="$2"
                    variant="outlined"
                    onPress={() =>
                        setShowPassword((v) => !v)
                    }
                    aria-label={
                        showPassword
                            ? "Hide password"
                            : "Show password"
                    }
                >
                    {showPassword ? <Eye size={16} /> : <EyeClosed size={16} />}
                </Button>
            </XStack>
            <Text fontSize={13} color="#64748b">
                For security, please enter your
                current password to confirm this
                change.
            </Text>
            <XStack
                display="flex"
                style={{
                    marginTop: 8,
                }}
            >
                <Button theme="dark_green" onPress={() => form.handleSubmit()}>
                    {
                        isPending && (
                            <Spinner
                                size="small"
                             />
                        )
                    }
                    Save Email
                </Button>
            </XStack>
        </YStack>
    );
};

export default UpdateEmail;
