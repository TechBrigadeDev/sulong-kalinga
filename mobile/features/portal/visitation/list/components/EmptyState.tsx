import { Calendar } from "lucide-react-native";
import { StyleSheet, View } from "react-native";
import { Text } from "tamagui";

interface EmptyStateProps {
    message?: string;
}

const EmptyState = ({
    message = "You don't have any visitations scheduled at the moment.",
}: EmptyStateProps) => {
    return (
        <View style={styles.container}>
            <Calendar size={64} color="#6b7280" />
            <View style={styles.textContainer}>
                <Text
                    fontSize="$6"
                    fontWeight="600"
                    color="#374151"
                    style={styles.title}
                >
                    No Visitations Scheduled
                </Text>
                <Text
                    fontSize="$4"
                    color="#6b7280"
                    style={styles.message}
                >
                    {message}
                </Text>
            </View>
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: "center",
        alignItems: "center",
        padding: 24,
        gap: 16,
    },
    textContainer: {
        alignItems: "center",
        gap: 8,
    },
    title: {
        textAlign: "center",
    },
    message: {
        textAlign: "center",
        maxWidth: 280,
        lineHeight: 20,
    },
});

export default EmptyState;
