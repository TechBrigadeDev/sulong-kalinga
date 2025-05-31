export interface CareWorkerFormData {
  firstName: string
  lastName: string
  birthday: Date
  gender: string
  civilStatus: string
  religion: string
  nationality: string
  educationalBackground: string
  address: string
  personalEmail: string
  mobileNumber: string
  landlineNumber: string
  sssId: string
  philhealthId: string
  pagibigId: string
  workEmail: string
  password: string
  confirmPassword: string
  municipality: string
  careManager: string
}

export interface FormSectionProps {
  formData: CareWorkerFormData
  setFormData: (data: CareWorkerFormData) => void
}
