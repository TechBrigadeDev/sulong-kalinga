import { Image, ImageProps } from "tamagui";

export const AppIcon = (props: ImageProps) => {
    return (
        <Image
            source={require("~/assets/images/icon.png")}
            {...props}
        />
    );
};
