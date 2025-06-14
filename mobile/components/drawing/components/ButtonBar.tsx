import React from "react";
import { View } from "react-native";
import { Button, Spinner } from "tamagui";
import { drawingStyles } from "../styles";
import { ButtonBarProps } from "../types";

export const ButtonBar: React.FC<
    ButtonBarProps
> = ({ onBack, onClear, onSave, isSaving }) => {
    return (
        <View style={drawingStyles.buttonBar}>
            <Button
                flex={1}
                mx="$2"
                bg="#6B7280"
                color="white"
                fontWeight="600"
                onPress={onBack}
            >
                Back
            </Button>

            <Button
                flex={1}
                mx="$2"
                bg="#EF4444"
                color="white"
                fontWeight="600"
                onPress={onClear}
            >
                Clear
            </Button>

            <Button
                flex={1}
                mx="$2"
                bg="#10B981"
                color="white"
                fontWeight="600"
                onPress={onSave}
                disabled={isSaving}
            >
                {isSaving && (
                    <Spinner size="small" />
                )}
                Save
            </Button>
        </View>
    );
};
