import { Input } from "tamagui";

import { useDebounce } from "~/common/hooks";

import { familyListStore } from "./store";

const FamilySearch = () => {
    const { setSearch } = familyListStore();

    const onSearch = useDebounce(
        (text: string) => {
            setSearch(text);
        },
        500,
    );

    return (
        <Input
            placeholder="Search Family Member"
            size="$3"
            onChangeText={onSearch}
        />
    );
};

export default FamilySearch;
