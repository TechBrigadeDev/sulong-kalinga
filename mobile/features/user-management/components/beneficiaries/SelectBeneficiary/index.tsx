import { useGetBeneficiaries } from "features/user-management/management.hook";
import {
    useEffect,
    useMemo,
    useState,
} from "react";
import {
    Adapt,
    Input,
    Select,
    Sheet,
    Spinner,
    YStack,
} from "tamagui";

import FlatList from "~/components/FlatList";
import { IBeneficiary } from "~/features/user-management/management.type";

interface SelectBeneficiaryProps {
    onValueChange: (
        beneficiary: IBeneficiary | null,
    ) => void;
    placeholder?: string;
    searchPlaceholder?: string;
    defaultValue?: IBeneficiary | null;
}

const SelectBeneficiary = ({
    onValueChange,
    placeholder:
        _placeholder = "Choose a beneficiary",
    searchPlaceholder = "Search beneficiaries...",
    defaultValue: _defaultValue = null,
}: SelectBeneficiaryProps) => {
    const [search, setSearch] = useState("");
    const [debouncedSearch, setDebouncedSearch] =
        useState("");

    // Debounce search to avoid too many API calls
    useEffect(() => {
        const timer = setTimeout(() => {
            setDebouncedSearch(search);
        }, 500);

        return () => clearTimeout(timer);
    }, [search]);

    const {
        data,
        isLoading,
        fetchNextPage,
        hasNextPage,
        isFetchingNextPage,
    } = useGetBeneficiaries({
        search: debouncedSearch,
    });

    // Get all beneficiaries from all pages
    const allBeneficiaries = useMemo(() => {
        return (
            data?.pages?.flatMap(
                (page) => page.data,
            ) || []
        );
    }, [data]);

    // Handle loading more data
    const handleLoadMore = () => {
        if (hasNextPage && !isFetchingNextPage) {
            fetchNextPage();
        }
    };

    const [beneficiary, setBeneficiary] =
        useState<string | null>(
            _defaultValue?.beneficiary_id?.toString() ||
                null,
        );

    const currentBeneficiary = useMemo(() => {
        if (_defaultValue) {
            return _defaultValue;
        }

        return allBeneficiaries.find(
            (b) =>
                b.beneficiary_id.toString() ===
                beneficiary,
        );
    }, [
        _defaultValue,
        allBeneficiaries,
        beneficiary,
    ]);

    useEffect(() => {
        // Set initial value if provided
        if (_defaultValue) {
            setBeneficiary(
                _defaultValue.beneficiary_id?.toString() ||
                    null,
            );
        }
    }, [_defaultValue]);

    // Handle search input change
    const handleSearchChange = (
        value: string,
    ) => {
        setSearch(value);
    };

    // Render footer with loading indicator for pagination
    const renderFooter = () => {
        if (isFetchingNextPage) {
            return (
                <YStack
                    style={{
                        padding: 8,
                        alignItems: "center",
                    }}
                >
                    <Spinner size="small" />
                </YStack>
            );
        }
        return null;
    };

    const renderBeneficiaryItem = ({
        item,
    }: {
        item: IBeneficiary;
    }) => {
        const index = allBeneficiaries.findIndex(
            (b) =>
                b.beneficiary_id ===
                item.beneficiary_id,
        );

        return (
            <Select.Item
                key={item.beneficiary_id}
                index={index}
                value={item.beneficiary_id.toString()}
            >
                <Select.ItemText>
                    {item.first_name}{" "}
                    {item.last_name}
                </Select.ItemText>
            </Select.Item>
        );
    };

    const renderContent = () => {
        if (isLoading) {
            return <Spinner size="large" />;
        }

        if (allBeneficiaries.length === 0) {
            return (
                <Select.Item
                    value="no-data"
                    index={0}
                >
                    <Select.ItemText>
                        No beneficiaries found
                    </Select.ItemText>
                </Select.Item>
            );
        }

        return (
            <FlatList
                data={allBeneficiaries}
                renderItem={renderBeneficiaryItem}
                estimatedItemSize={60}
                keyExtractor={(item, idx) =>
                    `${item.beneficiary_id}-${idx}`
                }
                showsVerticalScrollIndicator={
                    false
                }
                onEndReached={handleLoadMore}
                onEndReachedThreshold={0.5}
                ListFooterComponent={renderFooter}
                contentContainerStyle={{
                    paddingVertical: 8,
                }}
                showIndicators={false}
            />
        );
    };

    const [placeholder] = useState(
        currentBeneficiary
            ? `${currentBeneficiary.first_name} ${currentBeneficiary.last_name}`
            : _placeholder,
    );

    return (
        <Select
            defaultValue={
                currentBeneficiary?.beneficiary_id?.toString() ||
                undefined
            }
            value={beneficiary?.toString()}
            onValueChange={(value) => {
                const selectedBeneficiary =
                    allBeneficiaries.find(
                        (b) =>
                            b.beneficiary_id.toString() ===
                            value,
                    ) || null;

                setBeneficiary(
                    selectedBeneficiary?.beneficiary_id?.toString() ||
                        null,
                );
                onValueChange(
                    selectedBeneficiary || null,
                );
            }}
        >
            <Select.Trigger
                style={{
                    width: "100%",
                }}
            >
                <Select.Value
                    placeholder={placeholder}
                />
            </Select.Trigger>

            <Adapt when="maxMd" platform="touch">
                <Sheet modal>
                    <Sheet.Frame>
                        <Input
                            placeholder={
                                searchPlaceholder
                            }
                            value={search}
                            onChangeText={
                                handleSearchChange
                            }
                            size="$4"
                            marginInline="$2"
                        />
                        <Adapt.Contents />
                    </Sheet.Frame>
                    <Sheet.Overlay bg="transparent" />
                </Sheet>
            </Adapt>

            <Select.Content>
                <Select.ScrollUpButton />
                <Select.Viewport
                    bg="$white10"
                    p="$2"
                    style={{
                        maxHeight: 300,
                        minHeight: 200,
                    }}
                >
                    {renderContent()}
                </Select.Viewport>
                <Select.ScrollDownButton />
            </Select.Content>
        </Select>
    );
};

export default SelectBeneficiary;
