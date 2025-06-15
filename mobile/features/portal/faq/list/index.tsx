import FlatList from "components/FlatList";
import { useGetFAQ } from "features/portal/faq/hook";
import { I_FAQ } from "features/portal/faq/interface";
import { ChevronUp } from "lucide-react-native";
import {
    Accordion,
    Paragraph,
    Spinner,
    Square,
    Text,
    XStack,
} from "tamagui";

const FAQList = () => {
    const { data, isLoading } = useGetFAQ();

    if (isLoading) {
        return <Spinner size="large" />;
    }

    return (
        <Accordion type="multiple" flex={1}>
            <FlatList<I_FAQ>
                data={data || []}
                keyExtractor={(_, idx) =>
                    idx.toString()
                }
                renderItem={({ item }) => (
                    <FAQ faq={item} />
                )}
                showsVerticalScrollIndicator={
                    false
                }
                contentContainerStyle={{
                    padding: 16,
                }}
                ListEmptyComponent={
                    <XStack
                        flex={1}
                        content="center"
                        items="center"
                        p="$4"
                    >
                        <Text>
                            No FAQs available at
                            the moment.
                        </Text>
                    </XStack>
                }
            />
        </Accordion>
    );
};

const FAQ = ({ faq }: { faq: I_FAQ }) => {
    return (
        <Accordion.Item value={faq.question}>
            <Accordion.Trigger
                flexDirection="column"
                justify="space-between"
                display="flex"
            >
                {({
                    open,
                }: {
                    open: boolean;
                }) => (
                    <XStack
                        display="flex"
                        flexDirection="row"
                        items="center"
                        justify="space-between"
                        p="$2"
                    >
                        <Text
                            fontWeight="bold"
                            fontSize="$1"
                        >
                            {faq.question}
                        </Text>

                        <Square
                            animation="quick"
                            rotate={
                                open
                                    ? "180deg"
                                    : "0deg"
                            }
                        >
                            <ChevronUp
                                size={16}
                            />
                        </Square>
                    </XStack>
                )}
            </Accordion.Trigger>
            <Accordion.HeightAnimator animation="medium">
                <Accordion.Content
                    animation="medium"
                    exitStyle={{ opacity: 0 }}
                >
                    <Paragraph>
                        {faq.answer}
                    </Paragraph>
                </Accordion.Content>
            </Accordion.HeightAnimator>
        </Accordion.Item>
    );
};

export default FAQList;
