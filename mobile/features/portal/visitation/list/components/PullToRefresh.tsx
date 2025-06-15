import { RefreshControl } from "react-native";
import { ScrollView } from "tamagui";

interface Props {
    children: React.ReactNode;
    refreshing: boolean;
    onRefresh: () => void;
}

const PullToRefresh = ({
    children,
    refreshing,
    onRefresh,
}: Props) => {
    return (
        <ScrollView
            flex={1}
            refreshControl={
                <RefreshControl
                    refreshing={refreshing}
                    onRefresh={onRefresh}
                    tintColor="#0066cc"
                    colors={["#0066cc"]}
                />
            }
        >
            {children}
        </ScrollView>
    );
};

export default PullToRefresh;
