import { formatDate } from "common/date";
import FlatList from "components/FlatList";
import LoadingScreen from "components/loaders/LoadingScreen";
import { IReport } from "features/reports/type";
import {
    Eye,
    SquarePen,
} from "lucide-react-native";
import {
    Button,
    Card,
    H3,
    H5,
    Text,
    View,
    XStack,
    YStack,
} from "tamagui";

import { useCarePlans } from "~/features/reports/hook";

const ReportsList = () => {
    const { data, isLoading } = useCarePlans();

    if (isLoading) {
        return <LoadingScreen />;
    }

    if (!data || data.reports.length === 0) {
        return (
            <View>
                <Text>No reports found.</Text>
            </View>
        );
    }

    return (
        <FlatList
            data={data.reports}
            renderItem={({ item }) => (
                <ReportCard report={item} />
            )}
            keyExtractor={(item, index) =>
                `${item.beneficiary_id}-${index}`
            }
            contentContainerStyle={{
                padding: 16,
            }}
            ListEmptyComponent={
                <View>
                    <Text>No reports found.</Text>
                </View>
            }
        />
    );
};

const ReportCard = ({
    report,
}: {
    report: IReport;
}) => {
    const author = `${report.author_first_name} ${report.author_last_name}`;
    const beneficiary = `${report.beneficiary_first_name} ${report.beneficiary_last_name}`;
    const uploadedAt = formatDate(
        report.created_at,
        "MMM dd, yyyy",
    );

    return (
        <Card
            theme="light"
            marginBottom="$2"
            padding="$3"
            bg="#F8F9FA"
            borderRadius={8}
            borderColor="#E9ECEF"
            borderWidth={1}
        >
            <YStack
                display="flex"
                flexDirection="row"
                justify="space-between"
                items="center"
                borderBlockEndColor="$accentColor"
                borderBlockEndWidth="$0.5"
                paddingBlock="$2"
            >
                <YStack>
                    <H5>{author}</H5>
                </YStack>
                <XStack>
                    <Button
                        size="$3"
                        bg="#E9ECEF"
                        color="#495057"
                        borderColor="#DEE2E6"
                        variant="outlined"
                    >
                        <Eye size={16} />
                    </Button>
                    <Button
                        size="$3"
                        bg="#E9ECEF"
                        color="#495057"
                        borderColor="#DEE2E6"
                        variant="outlined"
                    >
                        <SquarePen size={16} />
                    </Button>
                </XStack>
            </YStack>
            <YStack>
                <XStack
                    display="flex"
                    flexDirection="row"
                    justify="space-between"
                    paddingBlock="$2"
                >
                    <Text>Beneficiary</Text>
                    <Text>{beneficiary}</Text>
                </XStack>
                <XStack
                    display="flex"
                    flexDirection="row"
                    justify="space-between"
                    paddingBlock="$2"
                >
                    <Text>Uploaded At</Text>
                    <Text>{uploadedAt}</Text>
                </XStack>
            </YStack>
        </Card>
    );
};

export default ReportsList;
