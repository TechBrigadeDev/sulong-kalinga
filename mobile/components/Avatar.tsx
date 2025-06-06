import { shapes } from "@dicebear/collection";
import { createAvatar } from "@dicebear/core";
import { generateId } from "common/generator";
import { Image as ExpoImage } from "expo-image";
import { useMemo } from "react";
import { StyleSheet } from "react-native";
import { SvgXml } from "react-native-svg";
import { View } from "tamagui";

type Props = {
    uri?: string | null;
    fallback?: string;
};

const AvatarImage = (props: Props) => {
    return (
        <View style={style.container}>
            {props.uri ? <Image uri={props.uri} /> : <Svg id={props.fallback} />}
        </View>
    );
};

const Image = ({ uri }: { uri: string }) => {
    return (
        <ExpoImage
            source={{
                uri,
            }}
            style={style.image}
            contentFit="cover"
        />
    );
};

const Svg = ({ id }: { id?: string }) => {
    const xml = useMemo(() => {
        return createAvatar(shapes, {
            seed: id ?? generateId(),
        }).toString();
    }, [id]);

    return (
        <View style={style.container}>
            <SvgXml xml={xml} style={style.image} />
        </View>
    );
};

const style = StyleSheet.create({
    container: {
        width: "100%",
        height: "100%",
        alignItems: "center",
        justifyContent: "center",
    },
    image: {
        flex: 1,
        width: "100%",
    },
});

export default AvatarImage;
