import { View } from "tamagui"
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
      if (!user) {
        return createAvatar(shapes, {
          seed: "default-avatar",
        }).toDataUri();
      }

      if (user?.photo) {
        return { uri: user.photo };
      }

      return createAvatar(shapes, {
        seed: user?.first_name + user?.last_name,
      }).toDataUri();
    }, [user]);

    return (
      <View style={style.container}>
        <Image
          source={source}
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