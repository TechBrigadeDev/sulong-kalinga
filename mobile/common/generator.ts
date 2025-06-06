import "react-native-get-random-values";

import hyperid from "hyperid";

export const generateId = () => {
    const id = hyperid();
    return id();
};
