import { shapes } from "@dicebear/collection";
import { createAvatar } from "@dicebear/core";
import { Image as ExpoImage } from "expo-image";
import { useMemo } from "react";
import { StyleSheet } from "react-native";
import { SvgXml } from "react-native-svg"; 
import { View } from "tamagui"

import { useUser } from "~/features/user/user.hook"

const AvatarImage = () => {
  const { data: user } = useUser();
  const Avatar = user?.photo ? Image : Svg;

  return (
    <View style={style.container}>
      <Avatar />
    </View>
  )
}

const Image = () => {
  const { data: user } = useUser();

  const source = useMemo(() => {
    let source = { uri: "" };
    if (user?.photo) {
      source.uri = user.photo;
    }

    source.uri = createAvatar(shapes, {
      seed: user?.id.toString() || "default-avatar",
    }).toDataUri();

    return source;
  }, [user]);

  return (
    <ExpoImage
      source={source}
      style={style.image}
      contentFit="cover"
    />
  );
}

const Svg = () => {
  const { data: user } = useUser();

  const xml = useMemo(() => {
    return createAvatar(shapes, {
      seed: user?.id.toString() ?? "default-avatar",
    }).toString(); // returns the raw SVG XML string
  }, [user]);

  return (
    <View style={style.container}>
      <SvgXml xml={xml} style={style.image} />
    </View>
  );
}

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
  }
})

export default AvatarImage;