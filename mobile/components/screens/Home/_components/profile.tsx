import { StyleSheet } from "react-native";
import { Avatar, H1, H2, H3, YStack } from "tamagui"
import AvatarImage from "~/components/Avatar"
import { useUser } from "~/features/user/user.hook";
import GradientBackground from "~/components/GradientContainer";
import { topBarHeight } from "~/constants/Layout";
import Badge from "~/components/Bagde";

const Profile = () => {
    const { data: user } = useUser();
    return (
      <GradientBackground>
        <YStack style={style.container}>
          <Avatar circular size="$10" marginBottom={10}>
            <AvatarImage/>
          </Avatar>
          <H3 style={style.name}>{user?.first_name} {user?.last_name}</H3>
          <Badge 
            variant={user?.status === 'Active' ? 'success' : 'warning'}
            style={style.shadow}
            size={15}
          >
            {user?.status}
          </Badge>
        </YStack>
      </GradientBackground>
    );
}

const style = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    marginTop: topBarHeight, 
    paddingBottom: 30,
  },
  name: {
    fontWeight: "bold",
    color: "#fff",
    marginBottom: 10,
  },
  shadow: {
    shadowColor: "#000",
    shadowOffset: {
      width: 0,
      height: 1,
    },
    shadowOpacity: 0.2,
    shadowRadius: 1.41,
  }
});

export default Profile;