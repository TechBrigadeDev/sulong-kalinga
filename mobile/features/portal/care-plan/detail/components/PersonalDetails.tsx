import { formatDate } from "common/date";
import { portalCarePlanDetailSchema } from "features/portal/care-plan/schema";
import {
    Calendar,
    FileText,
    User,
} from "lucide-react-native";
import {
    Card,
    H6,
    Text,
    XStack,
    YStack,
} from "tamagui";
import { z } from "zod";

type ICarePlanDetail = z.infer<
    typeof portalCarePlanDetailSchema
>;

interface PersonalDetailsProps {
    data: {
        beneficiary: ICarePlanDetail["beneficiary"];
        author: ICarePlanDetail["author"];
        care_worker: ICarePlanDetail["care_worker"];
        plan_date: string;
        status?: string;
    };
}

export default function PersonalDetails({
    data,
}: PersonalDetailsProps) {
    const {
        beneficiary,
        author,
        care_worker,
        plan_date,
        status,
    } = data;

    return (
        <Card bg="white" overflow="hidden">
            <Card.Header
                padded
                paddingBlock="$2"
                bg="#2d3748"
            >
                <XStack
                    items="center"
                    gap="$2"
                    justify="center"
                >
                    <FileText
                        size={20}
                        color="white"
                    />
                    <Text
                        color="white"
                        fontSize="$8"
                        fontWeight="bold"
                    >
                        Personal Information
                    </Text>
                </XStack>
            </Card.Header>

            <YStack p="$4" gap="$4">
                {/* Beneficiary Information */}
                <YStack gap="$3">
                    <XStack
                        items="center"
                        gap="$2"
                    >
                        <User
                            size={16}
                            color="#495057"
                        />
                        <H6 color="#495057">
                            Beneficiary Details
                        </H6>
                    </XStack>

                    <Card bg="#f8f9fa" p="$3">
                        <YStack gap="$3">
                            <XStack
                                justify="space-between"
                                items="center"
                            >
                                <Text
                                    fontSize="$3"
                                    color="#6c757d"
                                >
                                    Name
                                </Text>
                                <Text
                                    fontSize="$4"
                                    fontWeight="600"
                                    color="#495057"
                                >
                                    {
                                        beneficiary.first_name
                                    }{" "}
                                    {
                                        beneficiary.last_name
                                    }
                                </Text>
                            </XStack>

                            <XStack
                                justify="space-between"
                                items="center"
                            >
                                <Text
                                    fontSize="$3"
                                    color="#6c757d"
                                >
                                    Age
                                </Text>
                                <Text
                                    fontSize="$4"
                                    fontWeight="500"
                                    color="#495057"
                                >
                                    {beneficiary.birthday
                                        ? `${Math.floor((new Date().getTime() - new Date(beneficiary.birthday).getTime()) / (1000 * 60 * 60 * 24 * 365))} years old`
                                        : "N/A"}
                                </Text>
                            </XStack>

                            <XStack
                                justify="space-between"
                                items="center"
                            >
                                <Text
                                    fontSize="$3"
                                    color="#6c757d"
                                >
                                    Gender
                                </Text>
                                <Text
                                    fontSize="$4"
                                    fontWeight="500"
                                    color="#495057"
                                >
                                    {beneficiary.gender ||
                                        "N/A"}
                                </Text>
                            </XStack>

                            <XStack
                                justify="space-between"
                                items="center"
                            >
                                <Text
                                    fontSize="$3"
                                    color="#6c757d"
                                >
                                    Civil Status
                                </Text>
                                <Text
                                    fontSize="$4"
                                    fontWeight="500"
                                    color="#495057"
                                >
                                    {beneficiary.civil_status ||
                                        "N/A"}
                                </Text>
                            </XStack>

                            <XStack
                                justify="space-between"
                                items="center"
                            >
                                <Text
                                    fontSize="$3"
                                    color="#6c757d"
                                >
                                    Address
                                </Text>
                                <Text
                                    fontSize="$4"
                                    fontWeight="500"
                                    color="#495057"
                                    flexShrink={1}
                                    textAlign="right"
                                >
                                    {beneficiary.street_address ||
                                        "N/A"}
                                </Text>
                            </XStack>
                        </YStack>
                    </Card>
                </YStack>

                {/* Care Worker Information */}
                <YStack gap="$3">
                    <XStack
                        items="center"
                        gap="$2"
                    >
                        <User
                            size={16}
                            color="#495057"
                        />
                        <H6 color="#495057">
                            Care Worker
                        </H6>
                    </XStack>

                    <Card bg="#f8f9fa" p="$3">
                        <XStack
                            justify="space-between"
                            items="center"
                        >
                            <Text
                                fontSize="$3"
                                color="#6c757d"
                            >
                                Assigned Care
                                Worker
                            </Text>
                            <Text
                                fontSize="$4"
                                fontWeight="600"
                                color="#495057"
                            >
                                {
                                    care_worker.first_name
                                }{" "}
                                {
                                    care_worker.last_name
                                }
                            </Text>
                        </XStack>
                    </Card>
                </YStack>

                {/* Author Information */}
                <YStack gap="$3">
                    <XStack
                        items="center"
                        gap="$2"
                    >
                        <User
                            size={16}
                            color="#495057"
                        />
                        <H6 color="#495057">
                            Author
                        </H6>
                    </XStack>

                    <Card bg="#f8f9fa" p="$3">
                        <XStack
                            justify="space-between"
                            items="center"
                        >
                            <Text
                                fontSize="$3"
                                color="#6c757d"
                            >
                                Created By
                            </Text>
                            <Text
                                fontSize="$4"
                                fontWeight="600"
                                color="#495057"
                            >
                                {
                                    author.first_name
                                }{" "}
                                {author.last_name}
                            </Text>
                        </XStack>
                    </Card>
                </YStack>

                {/* Plan Date */}
                <YStack gap="$3">
                    <XStack
                        items="center"
                        gap="$2"
                    >
                        <Calendar
                            size={16}
                            color="#495057"
                        />
                        <H6 color="#495057">
                            Plan Information
                        </H6>
                    </XStack>

                    <Card bg="#f8f9fa" p="$3">
                        <YStack gap="$3">
                            <XStack
                                justify="space-between"
                                items="center"
                            >
                                <Text
                                    fontSize="$3"
                                    color="#6c757d"
                                >
                                    Date Created
                                </Text>
                                <Text
                                    fontSize="$4"
                                    fontWeight="500"
                                    color="#495057"
                                >
                                    {formatDate(
                                        new Date(
                                            plan_date,
                                        ),
                                        "MMM dd, yyyy",
                                    )}
                                </Text>
                            </XStack>

                            {status && (
                                <XStack
                                    justify="space-between"
                                    items="center"
                                >
                                    <Text
                                        fontSize="$3"
                                        color="#6c757d"
                                    >
                                        Status
                                    </Text>
                                    <Text
                                        fontSize="$4"
                                        fontWeight="600"
                                        color={
                                            status ===
                                            "acknowledged"
                                                ? "#059669"
                                                : "#f59e0b"
                                        }
                                    >
                                        {status
                                            .charAt(
                                                0,
                                            )
                                            .toUpperCase() +
                                            status.slice(
                                                1,
                                            )}
                                    </Text>
                                </XStack>
                            )}
                        </YStack>
                    </Card>
                </YStack>
            </YStack>
        </Card>
    );
}
