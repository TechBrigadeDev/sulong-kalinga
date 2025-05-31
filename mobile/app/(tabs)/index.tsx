import { StyleSheet } from 'react-native';
import { View } from 'tamagui';
import Home from '~/components/screens/Home/_components/home';

export default function HomeScreen() {
  return (
    <View style={styles.container}>
      <Home/>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
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
