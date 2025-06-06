import { careManagerListStore } from "features/user-management/components/care-managers/list/store";
import { Input } from "tamagui";

import { useDebounce } from "~/common/hooks";

const CareWorkerSearch = () => {
    const { setSearch } = careManagerListStore();

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
        />
    );
};

export default CareWorkerSearch;
