import { Input } from "tamagui";
import { familyListStore } from "./store"
import { useDebounce } from "~/common/hooks";

const FamilySearch = () => {
    const {
        setSearch,
    } = familyListStore();

    const onSearch = useDebounce((text: string) => {
        setSearch(text);
    }, 500);
 
    return (
        <Input
            placeholder="Search Family Member"
            size="$3"
            onChangeText={onSearch}
        />
    )
}

export default FamilySearch;
