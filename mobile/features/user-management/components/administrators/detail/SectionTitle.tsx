import { H4 } from "tamagui";

interface SectionTitleProps {
    children: React.ReactNode;
}

const SectionTitle = ({ children }: SectionTitleProps) => (
    <H4>{children}</H4>
);

export default SectionTitle;
