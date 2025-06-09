import { Input } from "tamagui";

import { useDebounce } from "~/common/hooks";

import { reportsListStore } from "./store";

const ReportsSearch = () => {
    const { setSearch } = reportsListStore();

    const onSearch = useDebounce(
        (text: string) => {
            setSearch(text);
        },
        500,
    );

    return (
        <Input
            placeholder="Search Reports"
            size="$3"
            onChangeText={onSearch}
        />
    );
};

export default ReportsSearch;
