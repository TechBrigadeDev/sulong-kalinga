import { uriToFileObject } from "common/file";

import {
    CarePlanFormData,
    InterventionData,
} from "./type";

/**
 * Maps the care plan form data to the expected backend API format
 * based on PHP validation rules
 * Returns FormData object for file upload support
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

    if (formData.personalDetails.illness) {
        formDataObj.append(
            "illness",
            formData.personalDetails.illness,
        );
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
            formDataObj.append(
                `selected_interventions[${index}]`,
                intervention.id,
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
                        intervention.categoryId,
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
    illness: string | null;
    evaluation_recommendations: string;
    photo: File;
    selected_interventions: string[];
    duration_minutes: number[];
    custom_category?: (string | null)[];
    custom_description?: (string | null)[];
    custom_duration?: number[];
}
