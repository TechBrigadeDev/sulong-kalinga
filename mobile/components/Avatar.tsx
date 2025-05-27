import { Text, View } from "tamagui"
import { Image } from "expo-image";
import { useUser } from "~/features/user/user.hook"
import { createAvatar } from "@dicebear/core";
import { shapes } from "@dicebear/collection";
import { useMemo } from "react";
import { StyleSheet } from "react-native";

const AvatarImage = () => {
    const { 
        data: user
    } = useUser();

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
      <View style={style.container}>
        <Image
          source={{
            uri: source.uri,
          }}
          style={style.image}
          contentFit="cover"
        />
      </View>
    )
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