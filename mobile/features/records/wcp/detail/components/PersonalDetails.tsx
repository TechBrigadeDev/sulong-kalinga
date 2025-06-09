import {
    Card,
    H4,
    Text,
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
    return (
        <Card bg="white" p="$4" space="$3">
            <YStack space="$2">
                <H4
                    color="#2c3e50"
                    fontWeight="600"
                >
                    Personal Information
                </H4>
            </YStack>

            <XStack space="$4">
                <YStack space="$2" flex={1}>
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

                <YStack space="$2" flex={1}>
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

            <XStack space="$4">
                <YStack space="$2" flex={1}>
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

                <YStack space="$2" flex={1}>
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

            <YStack space="$2">
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

            <YStack space="$2">
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
                    Pulmonary Disease, Age-related
                    {"\n"}Macular Degeneration,
                    {"\n"}Alzheimer&apos;s
                    Disease,{"\n"}Parkinson&apos;s
                    Disease
                </Text>
            </YStack>

            <XStack space="$4">
                <YStack space="$2" flex={1}>
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

                <YStack space="$2" flex={1}>
                    <Text
                        fontSize="$3"
                        color="#6c757d"
                        fontWeight="500"
                    >
                        Care Worker
                    </Text>
                    <Text
                        fontSize="$4"
                        fontWeight="600"
                        color="#007bff"
                    >
                        {data.care_worker}
                    </Text>
                </YStack>
            </XStack>
        </Card>
    );
}
