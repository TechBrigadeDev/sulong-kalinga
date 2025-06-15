import { useDebounce } from "common/hooks";
import { Search, X } from "lucide-react-native";
import { useState } from "react";
import { Button, Input, XStack } from "tamagui";

interface Props {
    value?: string;
    onSearch: (searchTerm: string) => void;
    placeholder?: string;
}

const SearchInput = ({
    value = "",
    onSearch,
    placeholder = "Search by author or date...",
}: Props) => {
    const [localValue, setLocalValue] = useState(value);

    const debouncedSearch = useDebounce((searchTerm: string) => {
        onSearch(searchTerm);
    }, 300);

    const handleChangeText = (text: string) => {
        setLocalValue(text);
        debouncedSearch(text);
    };

    const handleClear = () => {
        setLocalValue("");
        onSearch("");
    };

    return (
        <XStack
            alignItems="center"
            gap="$2"
            style={{
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
                placeholder={placeholder}
                value={localValue}
                onChangeText={handleChangeText}
                autoCapitalize="none"
                autoCorrect={false}
                placeholderTextColor="#999"
                borderWidth={0}
                flex={1}
                style={{
                    backgroundColor: "transparent",
                    fontSize: 16,
                }}
            />
            {localValue.length > 0 && (
                <Button
                    size="$2"
                    variant="outlined"
                    circular
                    onPress={handleClear}
                    style={{
                        backgroundColor: "transparent",
                        borderColor: "transparent",
                        padding: 4,
                    }}
                >
                    <X size={16} color="#666" />
                </Button>
            )}
        </XStack>
    );
};

export default SearchInput;
