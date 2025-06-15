// filepath: /Users/Shared/jjspscl/projects/sulong-kalinga/mobile/features/portal/medication/list/_components/Search.tsx
import { portalMedicationListStore } from "features/portal/medication/list/store";
import { Search, X } from "lucide-react-native";
import { useState } from "react";
import {
    Button,
    Input,
    InputProps,
    useDebounce,
    XStack,
} from "tamagui";

const PortalMedicationSearch = (
    props: InputProps,
) => {
    const { search, setSearch } =
        portalMedicationListStore();
    const [localValue, setLocalValue] =
        useState(search);

    const onSearch = useDebounce(
        (text: string) => {
            setSearch(text);
        },
        500,
    );

    const handleClear = () => {
        setLocalValue("");
        setSearch("");
    };

    const handleChangeText = (text: string) => {
        setLocalValue(text);
        onSearch(text);
    };

    return (
        <XStack
            gap="$2"
            style={{
                alignItems: "center",
                backgroundColor: "#ffffff",
                borderRadius: 8,
                borderColor: "#e5e7eb",
                borderWidth: 1,
                paddingHorizontal: 16,
                paddingVertical: 12,
                shadowColor: "#000",
                shadowOffset: {
                    width: 0,
                    height: 1,
                },
                shadowOpacity: 0.1,
                shadowRadius: 2,
                elevation: 2,
            }}
        >
            <Search size={20} color="#666" />
            <Input
                placeholder="Search medications..."
                value={localValue}
                onChangeText={handleChangeText}
                autoCapitalize="none"
                autoCorrect={false}
                placeholderTextColor="#999"
                borderWidth={0}
                flex={1}
                style={{
                    backgroundColor:
                        "transparent",
                    fontSize: 16,
                }}
                {...props}
            />
            {localValue.length > 0 && (
                <Button
                    size="$2"
                    variant="outlined"
                    circular
                    onPress={handleClear}
                    style={{
                        backgroundColor:
                            "transparent",
                        borderColor:
                            "transparent",
                        padding: 4,
                    }}
                >
                    <X size={16} color="#666" />
                </Button>
            )}
        </XStack>
    );
};

export default PortalMedicationSearch;
