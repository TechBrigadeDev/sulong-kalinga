import { careManagerListStore } from "features/user-management/components/care-managers/list/store";
import { useEffect } from "react";
import { Input } from "tamagui";

import { useDebounce } from "~/common/hooks";

interface Props {
    search?: string;
}
const CareWorkerSearch = ({
    search: searchQuery = "",
}: Props) => {
    const { setSearch } = careManagerListStore();

    useEffect(() => {
        if (searchQuery) {
            setSearch(searchQuery);
        }
    }, [searchQuery, setSearch]);

    const onSearch = useDebounce(
        (text: string) => {
            setSearch(text);
        },
        500,
    );

    return (
        <Input
            placeholder="Search Care Worker"
            size="$3"
            onChangeText={onSearch}
            defaultValue={searchQuery}
        />
    );
};

export default CareWorkerSearch;
