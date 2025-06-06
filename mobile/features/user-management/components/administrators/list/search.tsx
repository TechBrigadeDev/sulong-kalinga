import { Input } from "tamagui";

import { useDebounce } from "~/common/hooks";

import { adminListStore } from "./store";

const AdminSearch = () => {
    const { setSearch } = adminListStore();

    const onSearch = useDebounce(
        (text: string) => {
            setSearch(text);
        },
        500,
    );

    return (
        <Input
            placeholder="Search Administrator"
            size="$3"
            onChangeText={onSearch}
        />
    );
};

export default AdminSearch;
