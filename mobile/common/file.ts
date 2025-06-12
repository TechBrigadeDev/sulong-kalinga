import * as FileSystem from "expo-file-system";

/**
 * File upload utility for React Native/Expo
 */

export interface FileObject {
    uri: string;
    name: string;
    type: string;
}

/**
 * Converts a local file URI to a File object suitable for FormData uploads
 * @param uri - The local file URI (e.g., from ImagePicker or DocumentPicker)
 * @param customName - Optional custom filename
 * @returns Promise<File> - File object ready for FormData
 */
export async function uriToFileObject(
    uri: string,
    customName?: string,
): Promise<File> {
    try {
        console.log("Processing file URI:", uri);

        // Get file info to determine the file size and verify it exists
        const fileInfo =
            await FileSystem.getInfoAsync(uri);

        if (!fileInfo.exists) {
            throw new Error(
                "File does not exist at the specified URI",
            );
        }

        console.log("File info:", {
            exists: fileInfo.exists,
            size: fileInfo.size,
            isDirectory: fileInfo.isDirectory,
        });

        // Extract file extension from URI
        const uriParts = uri.split(".");
        const fileExtension =
            uriParts[
                uriParts.length - 1
            ].toLowerCase();

        // Extract filename from URI or use custom name
        let fileName: string;
        if (customName) {
            fileName = customName;
        } else {
            const pathParts = uri.split("/");
            const originalName =
                pathParts[pathParts.length - 1];
            fileName = originalName;
        }

        // Ensure filename has correct extension
        if (
            !fileName.endsWith(
                `.${fileExtension}`,
            )
        ) {
            fileName = `${fileName}.${fileExtension}`;
        }

        // Determine MIME type based on file extension
        const mimeType = getMimeType(
            fileExtension,
        );

        // For React Native/Expo, we need to create a File-like object
        // that works with FormData. We'll use the file URI directly.

        // Create a File-like object that FormData can handle
        const file = {
            uri,
            name: fileName,
            type: mimeType,
        } as any;

        // Add File-like properties and methods for better compatibility
        Object.defineProperty(file, "size", {
            value: fileInfo.size,
            writable: false,
        });

        Object.defineProperty(
            file,
            "lastModified",
            {
                value:
                    fileInfo.modificationTime ||
                    Date.now(),
                writable: false,
            },
        );

        // Add stream method for File API compatibility
        file.stream = () => {
            throw new Error(
                "stream() not supported in React Native",
            );
        };

        // Add text method for File API compatibility
        file.text = async () => {
            return await FileSystem.readAsStringAsync(
                uri,
            );
        };

        // Add arrayBuffer method for File API compatibility
        file.arrayBuffer = async () => {
            const base64 =
                await FileSystem.readAsStringAsync(
                    uri,
                    {
                        encoding:
                            FileSystem
                                .EncodingType
                                .Base64,
                    },
                );
            const binaryString = atob(base64);
            const bytes = new Uint8Array(
                binaryString.length,
            );
            for (
                let i = 0;
                i < binaryString.length;
                i++
            ) {
                bytes[i] =
                    binaryString.charCodeAt(i);
            }
            return bytes.buffer;
        };

        console.log("Generated file object:", {
            name: fileName,
            type: mimeType,
            size: fileInfo.size,
        });

        return file as File;
    } catch (error) {
        console.error(
            "Error converting URI to file object:",
            error,
        );
        throw new Error(
            `Failed to process file: ${error instanceof Error ? error.message : "Unknown error"}`,
        );
    }
}

/**
 * Gets the MIME type based on file extension
 * @param extension - File extension without the dot
 * @returns string - MIME type
 */
function getMimeType(extension: string): string {
    const mimeTypes: Record<string, string> = {
        // Images
        jpg: "image/jpeg",
        jpeg: "image/jpeg",
        png: "image/png",
        gif: "image/gif",
        webp: "image/webp",
        bmp: "image/bmp",
        svg: "image/svg+xml",

        // Documents
        pdf: "application/pdf",
        doc: "application/msword",
        docx: "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        xls: "application/vnd.ms-excel",
        xlsx: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        ppt: "application/vnd.ms-powerpoint",
        pptx: "application/vnd.openxmlformats-officedocument.presentationml.presentation",

        // Text
        txt: "text/plain",
        csv: "text/csv",

        // Archives
        zip: "application/zip",
        rar: "application/x-rar-compressed",

        // Audio
        mp3: "audio/mpeg",
        wav: "audio/wav",

        // Video
        mp4: "video/mp4",
        avi: "video/x-msvideo",
        mov: "video/quicktime",
    };

    return (
        mimeTypes[extension] ||
        "application/octet-stream"
    );
}

/**
 * Validates if the file is an image
 * @param file - The file object to validate
 * @returns boolean - True if the file is an image
 */
export function isImageFile(
    file: File | FileObject,
): boolean {
    return file.type.startsWith("image/");
}

/**
 * Validates file size
 * @param uri - File URI
 * @param maxSizeInMB - Maximum size in megabytes
 * @returns Promise<boolean> - True if file size is within limit
 */
export async function validateFileSize(
    uri: string,
    maxSizeInMB: number,
): Promise<boolean> {
    try {
        const fileInfo =
            await FileSystem.getInfoAsync(uri);
        if (!fileInfo.exists || !fileInfo.size) {
            return false;
        }

        const maxSizeInBytes =
            maxSizeInMB * 1024 * 1024;
        return fileInfo.size <= maxSizeInBytes;
    } catch (error) {
        console.error(
            "Error validating file size:",
            error,
        );
        return false;
    }
}
