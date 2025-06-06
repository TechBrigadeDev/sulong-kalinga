import FlatList from "components/FlatList";
import { useVisitations } from "features/scheduling/visitation/hook";
import { IVisitation } from "features/scheduling/visitation/type";
import { useMemo } from "react";
import {
    Card,
    GetThemeValueForKey,
    Text,
    YStack,
} from "tamagui";

const VisitationList = () => {
    const { data, isLoading } = useVisitations();

    if (!data || isLoading) {
        return null;
    }

    return (
        <FlatList<IVisitation>
            data={data}
            renderItem={({ item }) => (
                <Schedule visitation={item} />
            )}
        />
    );
};

const Schedule = ({
    visitation,
}: {
    visitation: IVisitation;
}) => {
    const beneficiary = `${visitation.beneficiary.first_name} ${visitation.beneficiary.last_name}`;
    const careWorker = `${visitation.care_worker.first_name} ${visitation.care_worker.last_name}`;

    const bg = useMemo<
        GetThemeValueForKey<"backgroundColor">
    >(() => {
        switch (visitation.status) {
            case "completed":
                return "$green5";
            case "canceled":
                return "$red10";
            case "scheduled":
                return "$blue8";
            default:
                return "$yellow1";
        }
    }, [visitation.status]);

    const color = useMemo<
        GetThemeValueForKey<"color">
    >(() => {
        switch (visitation.status) {
            case "completed":
                return "black";
            case "scheduled":
            case "canceled":
                return "white";
            default:
                return "$yellow11";
        }
    }, [visitation.status]);

    return (
        <Card
            theme="light"
            marginBottom="$2"
            bg={bg}
            borderRadius={8}
            borderColor="#E9ECEF"
            borderWidth={1}
        >
            <Card.Header>
                <YStack gap="$2">
                    <Text
                        fontSize="$5"
                        fontWeight="bold"
                        mb="$2"
                        color={color}
                    >
                        {beneficiary}
                    </Text>
                    <Text
                        fontSize="$4"
                        color={color}
                    >
                        Assigned to: {careWorker}
                    </Text>
                </YStack>
            </Card.Header>
            <YStack></YStack>
        </Card>
    );
};

export default VisitationList;
