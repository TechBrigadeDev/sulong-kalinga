import { Search } from "@tamagui/lucide-icons";
import { StyleSheet } from "react-native";
import { TextInput, XStack } from "tamagui";

interface SearchBarProps {
    value: string;
    onChangeText: (text: string) => void;
}

export const SearchBar = ({
    value,
    onChangeText,
}: SearchBarProps) => (
    <XStack style={styles.searchContainer}>
        <Search
            size={20}
            color="#999"
            style={styles.searchIcon}
        />
        <TextInput
            style={styles.searchInput}
            placeholder="Search"
            placeholderTextColor="#999"
            value={value}
            onChangeText={onChangeText}
        />
    </XStack>
);

const styles = StyleSheet.create({
    searchContainer: {
        padding: 16,
        flexDirection: "row",
        alignItems: "center",
        gap: 8,
        backgroundColor: "#f5f5f5",
        borderRadius: 8,
        marginHorizontal: 16,
        marginVertical: 8,
    },
    searchIcon: {
        marginLeft: 8,
    },
    searchInput: {
        flex: 1,
        fontSize: 16,
        padding: 8,
        color: "#333",
    },
});
