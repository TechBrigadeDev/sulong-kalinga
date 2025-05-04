import { Button, StyleSheet } from 'react-native';

import { useLogout } from '../../features/auth/auth.hook';
import { Text, View } from '../../components/Themed';
import EditScreenInfo from '../../components/EditScreenInfo';

export default function HomeScreen() {
  const {
    logout
  } = useLogout();

  const onPress = async () => {
    await logout();
  }

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Tab One</Text>
      <View style={styles.separator} lightColor="#eee" darkColor="rgba(255,255,255,0.1)" />
      <EditScreenInfo path="app/(tabs)/index.tsx" />
      <Button 
        title="Logout"
        onPress={onPress}
      />
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
});
