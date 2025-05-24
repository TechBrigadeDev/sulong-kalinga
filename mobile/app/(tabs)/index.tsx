import { StyleSheet } from 'react-native';

import { useLogout } from '~/features/auth/auth.hook';
import { View } from '~/components/Themed';

export default function HomeScreen() {
  const {
    logout
  } = useLogout();

  const onPress = async () => {
    await logout();
  }

  return (
    <View style={styles.container}>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  title: {
    fontSize: 20,
    fontWeight: 'bold',
  },
  separator: {
    marginVertical: 30,
    height: 1,
    width: '80%',
  },
  image: {
    width: 100,
    height: 100,
    marginBottom: 20,
  },
});
