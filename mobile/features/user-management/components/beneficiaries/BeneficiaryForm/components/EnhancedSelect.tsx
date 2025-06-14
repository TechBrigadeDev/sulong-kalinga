import React from "react";
import {
    Adapt,
    Label,
    Select,
    Sheet,
    Text,
    YStack,
} from "tamagui";

interface SelectOption {
    label: string;
    value: string;
}

interface EnhancedSelectProps {
    label: string;
    value: string;
    onValueChange: (value: string) => void;
    options: SelectOption[];
    placeholder?: string;
    error?: string;
    required?: boolean;
    flex?: number;
}

export const EnhancedSelect = ({
    label,
    value,
    onValueChange,
    options,
    placeholder = "Select an option",
    error,
    required = false,
    flex = 1,
}: EnhancedSelectProps) => {
    return (
        <YStack flex={flex} gap="$2">
            <Label fontWeight="600">
                {label}
                {required && (
                    <Text color="$red10"> *</Text>
                )}
            </Label>
            <Select
                value={value}
                onValueChange={onValueChange}
            >
                <Select.Trigger
                    borderColor={
                        error
                            ? "$red8"
                            : undefined
                    }
                    focusStyle={{
                        borderColor: error
                            ? "$red10"
                            : "$blue8",
                    }}
                >
                    <Select.Value
                        placeholder={placeholder}
                    />
                </Select.Trigger>

                <Adapt
                    when="maxMd"
                    platform="touch"
                >
                    <Sheet
                        modal
                        dismissOnSnapToBottom
                    >
                        <Sheet.Frame>
                            <Sheet.ScrollView>
                                <Adapt.Contents />
                            </Sheet.ScrollView>
                        </Sheet.Frame>
                        <Sheet.Overlay
                            animation="lazy"
                            enterStyle={{
                                opacity: 0,
                            }}
                            exitStyle={{
                                opacity: 0,
                            }}
                        />
                    </Sheet>
                </Adapt>

                <Select.Content zIndex={200000}>
                    <Select.ScrollUpButton />
                    <Select.Viewport
                        animation="quick"
                        animateOnly={[
                            "transform",
                            "opacity",
                        ]}
                        enterStyle={{
                            o: 0,
                            y: -10,
                        }}
                        exitStyle={{
                            o: 0,
                            y: 10,
                        }}
                        minHeight={200}
                        maxHeight={300}
                    >
                        {options.map(
                            (option, index) => (
                                <Select.Item
                                    key={
                                        option.value
                                    }
                                    index={index}
                                    value={
                                        option.value
                                    }
                                >
                                    <Select.ItemText>
                                        {
                                            option.label
                                        }
                                    </Select.ItemText>
                                </Select.Item>
                            ),
                        )}
                    </Select.Viewport>
                    <Select.ScrollDownButton />
                </Select.Content>
            </Select>
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

export default EnhancedSelect;
