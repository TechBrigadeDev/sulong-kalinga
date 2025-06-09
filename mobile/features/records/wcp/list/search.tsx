import { Input } from "tamagui";

import { useDebounce } from "~/common/hooks";

import { wcpRecordsListStore } from "./store";

const WCPRecordsSearch = () => {
    const { setSearch } = wcpRecordsListStore();

    const onSearch = useDebounce(
        (text: string) => {
            setSearch(text);
        },
        500,
    );

    return (
        <Input
            placeholder="Search WCP Records"
            size="$3"
            onChangeText={onSearch}
        />
    );
};

export default WCPRecordsSearch;
