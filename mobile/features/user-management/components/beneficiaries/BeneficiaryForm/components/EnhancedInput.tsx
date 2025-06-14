import React from "react";
import {
    Input,
    Label,
    Text,
    YStack,
    InputProps,
} from "tamagui";

interface EnhancedInputProps extends InputProps {
    label: string;
    error?: string;
    helperText?: string;
    required?: boolean;
    flex?: number;
}

export const EnhancedInput = ({
    label,
    error,
    helperText,
    required = false,
    flex = 1,
    ...inputProps
}: EnhancedInputProps) => {
    return (
        <YStack flex={flex} gap="$2">
            <Label fontWeight="600">
                {label}
                {required && (
                    <Text color="$red10"> *</Text>
                )}
            </Label>
            <Input
                size="$4"
                {...inputProps}
                borderColor={
                    error ? "$red8" : undefined
                }
                focusStyle={{
                    borderColor: error
                        ? "$red10"
                        : "$blue8",
                }}
            />
            {helperText && (
                <Text opacity={0.6} fontSize="$2">
                    {helperText}
                </Text>
            )}
            {error && (
                <Text
                    color="$red10"
                    fontSize="$2"
                >
                    {error}
                </Text>
            )}
        </YStack>
    );
};

export default EnhancedInput;
