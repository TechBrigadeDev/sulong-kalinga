import { z } from "zod";

import {
    appointmentStatusSchema,
    appointmentTypeSchema,
    internalAppointmentSchema,
    occurrenceSchema,
    participantSchema,
    participantTypeSchema,
} from "./schema";

export type IAppointmentStatus = z.infer<
    typeof appointmentStatusSchema
>;

export type IParticipantType = z.infer<
    typeof participantTypeSchema
>;

export type IAppointmentType = z.infer<
    typeof appointmentTypeSchema
>;

export type IParticipant = z.infer<
    typeof participantSchema
>;

export type IOccurrence = z.infer<
    typeof occurrenceSchema
>;

export type IInternalAppointment = z.infer<
    typeof internalAppointmentSchema
>;
