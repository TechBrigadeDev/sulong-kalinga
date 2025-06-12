import { useRouter } from "expo-router";
import { careWorkerListStore } from "features/user-management/components/care-workers/list/store";
import { Pressable } from "react-native";
import {
    Card,
    Text,
    View,
    XStack,
    YStack,
} from "tamagui";

export interface PersonalDetailsProps {
    data: {
        beneficiary: string;
        care_worker: string;
        plan_date: string;
    };
}

export function PersonalDetails({
    data,
}: PersonalDetailsProps) {
    const router = useRouter();
    const { setSearch } = careWorkerListStore();

    const onPressCareWorker = () => {
        setSearch(data.care_worker);
        router.push(
            `/(tabs)/options/user-management/care-workers?search=${data.care_worker}`,
        );
    };
    return (
        <Card bg="white" overflow="hidden">
            <Card.Header
                padded
                paddingBlock="$2"
                bg="#2d3748"
            >
                <View
                    display="flex"
                    flexDirection="row"
                    gap="$2"
                    items="center"
                    justify="center"
                >
                    <Text
                        color="white"
                        fontSize="$8"
                        fontWeight="bold"
                    >
                        Personal Details
                    </Text>
                </View>
            </Card.Header>
            <YStack p="$4" gap="$4">
                <XStack gap="$4">
                    <YStack gap="$2" flex={1}>
                        <Text
                            fontSize="$3"
                            color="#6c757d"
                            fontWeight="500"
                        >
                            Beneficiary Name
                        </Text>
                        <Text
                            fontSize="$5"
                            fontWeight="600"
                            color="#495057"
                        >
                            {data.beneficiary}
                        </Text>
                    </YStack>

                    <YStack gap="$2" flex={1}>
                        <Text
                            fontSize="$3"
                            color="#6c757d"
                            fontWeight="500"
                        >
                            Gender
                        </Text>
                        <Text
                            fontSize="$4"
                            color="#495057"
                        >
                            Male
                        </Text>
                    </YStack>
                </XStack>

                <XStack gap="$4">
                    <YStack gap="$2" flex={1}>
                        <Text
                            fontSize="$3"
                            color="#6c757d"
                            fontWeight="500"
                        >
                            Age
                        </Text>
                        <Text
                            fontSize="$4"
                            color="#495057"
                        >
                            72 years old
                        </Text>
                    </YStack>

                    <YStack gap="$2" flex={1}>
                        <Text
                            fontSize="$3"
                            color="#6c757d"
                            fontWeight="500"
                        >
                            Birthdate
                        </Text>
                        <Text
                            fontSize="$4"
                            color="#495057"
                        >
                            March 15, 1952
                        </Text>
                    </YStack>
                </XStack>

                <YStack gap="$2">
                    <Text
                        fontSize="$3"
                        color="#6c757d"
                        fontWeight="500"
                    >
                        Address
                    </Text>
                    <Text
                        fontSize="$4"
                        color="#495057"
                    >
                        63 Magaaysay Street,{"\n"}
                        Barangay Balud
                    </Text>
                </YStack>

                <YStack gap="$2">
                    <Text
                        fontSize="$3"
                        color="#6c757d"
                        fontWeight="500"
                    >
                        Medical Conditions
                    </Text>
                    <Text
                        fontSize="$4"
                        color="#495057"
                    >
                        Chronic Obstructive{"\n"}
                        Pulmonary Disease,
                        Age-related
                        {"\n"}Macular
                        Degeneration,
                        {"\n"}Alzheimer&apos;s
                        Disease,{"\n"}
                        Parkinson&apos;s Disease
                    </Text>
                </YStack>

                <XStack gap="$4">
                    <YStack gap="$2" flex={1}>
                        <Text
                            fontSize="$3"
                            color="#6c757d"
                            fontWeight="500"
                        >
                            Plan Date
                        </Text>
                        <Text
                            fontSize="$4"
                            fontWeight="600"
                            color="#007bff"
                        >
                            {data.plan_date}
                        </Text>
                    </YStack>

                    <YStack gap="$2" flex={1}>
                        <Text
                            fontSize="$3"
                            color="#6c757d"
                            fontWeight="500"
                        >
                            Author
                        </Text>
                        <Pressable
                            onPress={
                                onPressCareWorker
                            }
                        >
                            <Text
                                fontSize="$4"
                                fontWeight="600"
                                color="#007bff"
                            >
                                {data.care_worker}
                            </Text>
                        </Pressable>
                    </YStack>
                </XStack>
            </YStack>
        </Card>
    );
}
