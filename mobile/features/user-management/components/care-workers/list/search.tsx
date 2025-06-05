import { Input } from "tamagui";
import { useDebounce } from "~/common/hooks";
import { careManagerListStore } from "../../care-managers/list/store";

const CareWorkerSearch = () => {
    const {
        setSearch,
    } = careManagerListStore();

    const onSearch = useDebounce((text: string) => {
        setSearch(text);
    }, 500);
 
    return (
        <Input
            placeholder="Search Care Worker"
            size="$3"
            onChangeText={onSearch}
        />
    )
}

export default CareWorkerSearch;
