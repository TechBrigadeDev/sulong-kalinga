import { BarChart3 } from "lucide-react-native";
import { Button, Text, XStack } from "tamagui";

interface Props {
    onStatisticsPress?: () => void;
}

const CarePlanHeader = ({
    onStatisticsPress,
}: Props) => {
    return (
        <XStack
            justifyContent="space-between"
            alignItems="center"
            p="$4"
            pb="$2"
            bg="$background"
        >
            <Text
                fontSize="$8"
                fontWeight="bold"
                color="$color"
            >
                CARE PLAN RECORDS
            </Text>
            <Button
                size="$3"
                theme="blue"
                onPress={onStatisticsPress}
                icon={<BarChart3 size={16} />}
                bg="$blue8"
                color="white"
                borderRadius="$3"
            >
                Care Plan Statistics
            </Button>
        </XStack>
    );
};

export default CarePlanHeader;
