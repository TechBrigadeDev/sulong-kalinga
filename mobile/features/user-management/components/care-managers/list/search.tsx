import { Input } from "tamagui";

import { useDebounce } from "~/common/hooks";

import { careManagerListStore } from "./store";

const CareManagerSearch = () => {
    const { setSearch } = careManagerListStore();

    const onSearch = useDebounce(
        (text: string) => {
            setSearch(text);
        },
        500,
    );

    return (
        <Input
            placeholder="Search Care Managers"
            size="$3"
            onChangeText={onSearch}
        />
    );
};

export default CareManagerSearch;
