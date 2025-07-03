import { uriToFileObject } from "common/file";

import {
    CarePlanFormData,
    InterventionData,
} from "./type";

/**
 * Maps the care plan form data to the expected backend API format
 * based on PHP validation rules
 * Returns FormData object for file upload support
 *
 * Note: The illness field is treated as a comma-separated string in the form
 * but mapped to separate illness[x] array entries for the backend API.
 * This allows multiple illnesses to be sent as individual form data entries.
 */
export async function mapCarePlanFormToApiData(
    formData: CarePlanFormData,
): Promise<FormData> {
    // Collect all interventions from different categories
    const allInterventions: InterventionData[] = [
        ...formData.mobility,
        ...formData.cognitive,
        ...formData.selfSustainability,
        ...formData.diseaseTherapy,
        ...formData.socialContact,
        ...formData.outdoorActivity,
        ...formData.householdKeeping,
    ];

    // Separate standard and custom interventions
    const standardInterventions =
        allInterventions.filter(
            (intervention) =>
                !intervention.isCustom,
        );
    const customInterventions =
        allInterventions.filter(
            (intervention) =>
                intervention.isCustom,
        );

    // Create FormData object
    const formDataObj = new FormData();

    // Personal details mapping
    formDataObj.append(
        "beneficiary_id",
        formData.personalDetails.beneficiaryId,
    );
    formDataObj.append(
        "assessment",
        formData.personalDetails.assessment,
    );
    formDataObj.append(
        "blood_pressure",
        formData.personalDetails.bloodPressure,
    );
    formDataObj.append(
        "body_temperature",
        formData.personalDetails.temperature.toString(),
    );
    formDataObj.append(
        "pulse_rate",
        formData.personalDetails.pulseRate.toString(),
    );
    formDataObj.append(
        "respiratory_rate",
        formData.personalDetails.respiratoryRate.toString(),
    );

    // Handle illness as array for comma-separated illnesses
    if (formData.personalDetails.illness) {
        // Split by comma, trim whitespace, and filter out empty strings
        const illnessArray =
            formData.personalDetails.illness
                .split(",")
                .map((illness) => illness.trim())
                .filter(
                    (illness) =>
                        illness.length > 0,
                );

        // Append each illness as illness[index] to match backend expectations
        illnessArray.forEach((illness, index) => {
            formDataObj.append(
                `illness[${index}]`,
                illness,
            );
        });
    }

    // Evaluation mapping
    formDataObj.append(
        "evaluation_recommendations",
        formData.evaluation.recommendations,
    );

    // Handle photo upload
    if (formData.evaluation.pictureUri) {
        try {
            const fileObject =
                await uriToFileObject(
                    formData.evaluation
                        .pictureUri,
                    "care_plan_photo",
                );

            console.log(
                "File object created for photo upload:",
                fileObject,
            );
            formDataObj.append(
                "photo",
                fileObject as any,
            );
        } catch (error) {
            console.error(
                "Error processing image file:",
                error,
            );
            throw new Error(
                "Failed to process the selected image",
            );
        }
    }

    // Standard interventions mapping
    standardInterventions.forEach(
        (intervention, index) => {
            // Use interventionId (database intervention_id) for standard interventions
            const interventionIdToUse =
                intervention.interventionId ||
                intervention.id;
            formDataObj.append(
                `selected_interventions[${index}]`,
                interventionIdToUse.toString(),
            );
            formDataObj.append(
                `duration_minutes[${index}]`,
                intervention.minutes.toString(),
            );
        },
    );

    // Custom interventions mapping (only if there are custom interventions)
    if (customInterventions.length > 0) {
        customInterventions.forEach(
            (intervention, index) => {
                if (intervention.categoryId) {
                    formDataObj.append(
                        `custom_category[${index}]`,
                        intervention.categoryId.toString(),
                    );
                }
                if (intervention.description) {
                    formDataObj.append(
                        `custom_description[${index}]`,
                        intervention.description,
                    );
                }
                formDataObj.append(
                    `custom_duration[${index}]`,
                    intervention.minutes.toString(),
                );
            },
        );
    }

    return formDataObj;
}

/**
 * Type definition for the API payload structure
 * based on PHP validation rules
 */
export interface CarePlanApiData {
    beneficiary_id: string;
    assessment: string;
    blood_pressure: string;
    body_temperature: number;
    pulse_rate: number;
    respiratory_rate: number;
    illness: string[] | null; // Changed to string array to reflect the array mapping
    evaluation_recommendations: string;
    photo: File;
    selected_interventions: string[];
    duration_minutes: number[];
    custom_category?: (string | null)[];
    custom_description?: (string | null)[];
    custom_duration?: number[];
}
