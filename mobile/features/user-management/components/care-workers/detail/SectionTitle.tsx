import { Text } from "tamagui";

const SectionTitle = ({ children }: { children: React.ReactNode }) => (
    <Text fontSize="$6" fontWeight="$6">
        {children}
    </Text>
);

export default SectionTitle;
