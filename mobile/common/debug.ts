export const log = (...args: any[]) => {
    console.log("\x1b[2J");
    console.log(...args, "\n\n");
};
