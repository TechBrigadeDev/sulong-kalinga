<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\QueryException;

class UserManagementService
{
    public function deleteCareworker($id, $currentUser)
    {
        try {
            Log::info('Starting deletion process for care worker ID: ' . $id);
            
            // Find the care worker
            $careworker = User::where('id', $id)
                            ->where('role_id', 3) // Care worker role
                            ->first();
            
            if (!$careworker) {
                Log::warning('Care worker not found: ' . $id);
                return [
                    'success' => false,
                    'message' => 'Care worker not found.'
                ];
            }
            
            Log::info('Found care worker: ' . $careworker->first_name . ' ' . $careworker->last_name);
            
            // Check if the current user is authorized (admin or care manager)
            if ($currentUser->role_id != 1 && $currentUser->role_id != 2) {
                Log::warning('Unauthorized deletion attempt. User ID: ' . $currentUser->id);
                return [
                    'success' => false,
                    'message' => 'Only administrators or care managers can delete care worker accounts.'
                ];
            }
            
            // Check for dependencies before attempting to delete
            Log::info('Checking dependencies for care worker ID: ' . $id);
            
                // 1. Check if the care worker has any assigned beneficiaries via general_care_plans
                $assignedCareWorkerPlans = DB::table('general_care_plans')
                ->where('care_worker_id', $id)
                ->get();

                Log::info('Number of care plans assigned to this care worker: ' . count($assignedCareWorkerPlans));

                if (count($assignedCareWorkerPlans) > 0) {
                // Now check if any of these care plans are linked to beneficiaries
                $careplanIds = $assignedCareWorkerPlans->pluck('id')->toArray();

                $linkedBeneficiaries = DB::table('beneficiaries')
                    ->whereIn('general_care_plan_id', $careplanIds)
                    ->count();

                Log::info('Number of beneficiaries linked to this care worker\'s care plans: ' . $linkedBeneficiaries);

                if ($linkedBeneficiaries > 0) {
                    Log::info('Care worker has assigned beneficiaries, cannot delete');
                    return [
                        'success' => false,
                        'message' => "This care worker is assigned to {$linkedBeneficiaries} beneficiaries through care plans. Please edit the beneficiary profiles and reassign these responsibilities to another care worker in the Care Worker's Responsibilities Section.",
                        'error_type' => 'dependency_beneficiaries'
                    ];
                }
            }
            
            // 2. Check weekly care plans table for created_by/updated_by references
            $carePlanReferences = DB::table('weekly_care_plans')
                ->where('created_by', $id)
                ->orWhere('updated_by', $id)
                ->count();
            
            Log::info('Care plan references count: ' . $carePlanReferences);
                
            if ($carePlanReferences > 0) {
                Log::info('Care worker has care plan references, cannot delete');
                return [
                    'success' => false,
                    'message' => 'This care worker has created or updated care plans which require audit history to be maintained.',
                    'error_type' => 'dependency_care_plans'
                ];
            }
            
            // 3. Check users table for updated_by references only
            $userReferences = DB::table('cose_users')
                ->where('updated_by', $id)
                ->count();
            
            Log::info('User references count: ' . $userReferences);
                
            if ($userReferences > 0) {
                Log::info('Care worker has user references, cannot delete');
                return [
                    'success' => false,
                    'message' => 'This care worker has updated user accounts which require audit history to be maintained.',
                    'error_type' => 'dependency_users'
                ];
            }
            
            // 4. Check family members table for created_by/updated_by references
            $familyReferences = DB::table('family_members')
                ->where('created_by', $id)
                ->orWhere('updated_by', $id)
                ->count();
            
            Log::info('Family references count: ' . $familyReferences);
                
            if ($familyReferences > 0) {
                Log::info('Care worker has family references, cannot delete');
                return [
                    'success' => false,
                    'message' => 'This care worker has created or updated family member records which require audit history to be maintained.',
                    'error_type' => 'dependency_family'
                ];
            }
            
            // If we got here, we can proceed with deletion attempt
            Log::info('No dependencies found, proceeding with deletion');
            
            // Begin database transaction
            DB::beginTransaction();
            
            try {
                // Try to delete the care worker
                $result = $careworker->delete();
                Log::info('Delete result: ' . ($result ? 'success' : 'failed'));
                
                if (!$result) {
                    // If regular delete fails, try with force delete
                    Log::info('Regular delete failed, trying force delete');
                    $result = $careworker->forceDelete();
                    Log::info('Force delete result: ' . ($result ? 'success' : 'failed'));
                    
                    if (!$result) {
                        throw new Exception('Both regular and force delete failed');
                    }
                }
                
                // If we got here, everything succeeded
                DB::commit();
                Log::info('Care worker deleted successfully: ' . $id);
                
                return [
                    'success' => true,
                    'message' => 'Care worker deleted successfully.'
                ];
                
            } catch (Exception $innerException) {
                // If anything fails, roll back the transaction
                DB::rollBack();
                Log::error('Inner exception while deleting care worker: ' . $innerException->getMessage());
                Log::error('Stack trace: ' . $innerException->getTraceAsString());
                throw $innerException;
            }
            
        } catch (QueryException $e) {
            Log::error('Database error deleting care worker: ' . $e->getMessage());
            Log::error('Error code: ' . $e->getCode());
            
            // Check for foreign key constraint violation
            if (in_array($e->getCode(), ['23000', '23503']) || 
                (is_numeric($e->getCode()) && in_array((int)$e->getCode(), [23000, 23503]))) {
                
                // Try to determine which table has the dependency
                $errorMsg = $e->getMessage();
                Log::error('Full error message: ' . $errorMsg);
                
                if (stripos($errorMsg, 'beneficiaries') !== false || stripos($errorMsg, 'general_care_plans') !== false) {
                    return [
                        'success' => false,
                        'message' => 'This care worker is assigned to beneficiaries through their general care plan. Please edit the beneficiary profiles and reassign these responsibilities to another care worker in the Care Worker\'s Responsibilities Section.',
                        'error_type' => 'dependency_beneficiaries'
                    ];
                }
                else if (stripos($errorMsg, 'weekly_care_plans') !== false || stripos($errorMsg, 'care_plans') !== false) {
                    return [
                        'success' => false,
                        'message' => 'This care worker has created or updated care plans which require audit history to be maintained.',
                        'error_type' => 'dependency_care_plans'
                    ];
                } else if (stripos($errorMsg, 'cose_users') !== false || stripos($errorMsg, 'users') !== false) {
                    return [
                        'success' => false,
                        'message' => 'This care worker has updated user accounts which require audit history to be maintained.',
                        'error_type' => 'dependency_users'
                    ];
                } else if (stripos($errorMsg, 'family_members') !== false) {
                    return [
                        'success' => false,
                        'message' => 'This care worker has created or updated family member records which require audit history to be maintained.',
                        'error_type' => 'dependency_family'
                    ];
                }
                
                // Generic dependency message for other tables
                return [
                    'success' => false,
                    'message' => 'This care worker has created or updated records in the system that require audit history to be maintained.',
                    'error_type' => 'dependency_audit'
                ];
            }
            
            // If in development, return detailed error
            if (app()->environment('local', 'development')) {
                return [
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage(),
                    'error_type' => 'database',
                    'code' => $e->getCode()
                ];
            }
            
            return [
                'success' => false,
                'message' => 'A database error occurred. Please try again later or contact the system administrator.',
                'error_type' => 'database'
            ];
        } catch (Exception $e) {
            Log::error('General error deleting care worker: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // If in development, return detailed error
            if (app()->environment('local', 'development')) {
                return [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'error_type' => 'general',
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ];
            }
            
            return [
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error_type' => 'general'
            ];
        }
    }

    public function deleteFamilyMember($id, $currentUser)
    {
        try {
            Log::info('Starting deletion process for family member ID: ' . $id);
            
            // Find the family member
            $familyMember = DB::table('family_members')
                            ->where('family_member_id', $id)
                            ->first();
            
            if (!$familyMember) {
                Log::warning('Family member not found: ' . $id);
                return [
                    'success' => false,
                    'message' => 'Family member not found.'
                ];
            }
            
            Log::info('Found family member: ' . $familyMember->first_name . ' ' . $familyMember->last_name);
            
            // Check if the current user is authorized (admin or care manager)
            if ($currentUser->role_id != 1 && $currentUser->role_id != 2) {
                Log::warning('Unauthorized deletion attempt. User ID: ' . $currentUser->id);
                return [
                    'success' => false,
                    'message' => 'Only administrators or care managers can delete family member accounts.'
                ];
            }
            
            // Check for dependencies before attempting to delete
            Log::info('Checking dependencies for family member ID: ' . $id);
            
            // 1. Check weekly care plans table for acknowledgements by this family member
            $carePlanReferences = DB::table('weekly_care_plans')
                ->where('acknowledged_by_family', $id)
                ->count();
            
            Log::info('Care plan acknowledgements count: ' . $carePlanReferences);
                
            if ($carePlanReferences > 0) {
                Log::info('Family member has care plan acknowledgements, cannot delete');
                return [
                    'success' => false,
                    'message' => 'This family member has acknowledged care plans which require audit history to be maintained.',
                    'error_type' => 'dependency_care_plans'
                ];
            }
            
            // If we got here, we can proceed with deletion attempt
            Log::info('No dependencies found, proceeding with deletion');
            
            // Begin database transaction
            DB::beginTransaction();
            
            try {
                // Try to delete the family member
                $result = DB::table('family_members')->where('family_member_id', $id)->delete();
                Log::info('Delete result: ' . ($result ? 'success' : 'failed'));
                
                if (!$result) {
                    throw new Exception('Failed to delete family member');
                }
                
                // If we got here, everything succeeded
                DB::commit();
                Log::info('Family member deleted successfully: ' . $id);
                
                return [
                    'success' => true,
                    'message' => 'Family member deleted successfully.'
                ];
                
            } catch (Exception $innerException) {
                // If anything fails, roll back the transaction
                DB::rollBack();
                Log::error('Inner exception while deleting family member: ' . $innerException->getMessage());
                Log::error('Stack trace: ' . $innerException->getTraceAsString());
                throw $innerException;
            }
            
        } catch (QueryException $e) {
            Log::error('Database error deleting family member: ' . $e->getMessage());
            Log::error('Error code: ' . $e->getCode());
            
            // Check for foreign key constraint violation
            if (in_array($e->getCode(), ['23000', '23503']) || 
                (is_numeric($e->getCode()) && in_array((int)$e->getCode(), [23000, 23503]))) {
                
                // Try to determine which table has the dependency
                $errorMsg = $e->getMessage();
                Log::error('Full error message: ' . $errorMsg);
                
                if (stripos($errorMsg, 'weekly_care_plans') !== false || stripos($errorMsg, 'care_plans') !== false) {
                    return [
                        'success' => false,
                        'message' => 'This family member has acknowledged care plans which require audit history to be maintained.',
                        'error_type' => 'dependency_care_plans'
                    ];
                } 
                
                // Generic dependency message for other tables (for future dependencies)
                return [
                    'success' => false,
                    'message' => 'This family member has records in the system that require audit history to be maintained.',
                    'error_type' => 'dependency_audit'
                ];
            }
            
            // If in development, return detailed error
            if (app()->environment('local', 'development')) {
                return [
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage(),
                    'error_type' => 'database',
                    'code' => $e->getCode()
                ];
            }
            
            return [
                'success' => false,
                'message' => 'A database error occurred. Please try again later or contact the system administrator.',
                'error_type' => 'database'
            ];
        } catch (Exception $e) {
            Log::error('General error deleting family member: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // If in development, return detailed error
            if (app()->environment('local', 'development')) {
                return [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'error_type' => 'general',
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ];
            }
            
            return [
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error_type' => 'general'
            ];
        }
    }

    public function deleteBeneficiary($id, $currentUser)
    {
        try {
            Log::info('Starting deletion process for beneficiary ID: ' . $id);
            
            // Find the beneficiary
            $beneficiary = DB::table('beneficiaries')
                            ->where('beneficiary_id', $id)
                            ->first();
            
            if (!$beneficiary) {
                Log::warning('Beneficiary not found: ' . $id);
                return [
                    'success' => false,
                    'message' => 'Beneficiary not found.'
                ];
            }
            
            Log::info('Found beneficiary: ' . $beneficiary->first_name . ' ' . $beneficiary->last_name);
            
            // Check if the current user is authorized (admin or care manager)
            if ($currentUser->role_id != 1 && $currentUser->role_id != 2) {
                Log::warning('Unauthorized deletion attempt. User ID: ' . $currentUser->id);
                return [
                    'success' => false,
                    'message' => 'Only administrators or care managers can delete beneficiary profiles.'
                ];
            }
            
            // Check for dependencies before attempting to delete
            Log::info('Checking dependencies for beneficiary ID: ' . $id);
            
            // 1. Check for related family members
            $familyReferences = DB::table('family_members')
                ->where('related_beneficiary_id', $id)
                ->count();
            
            Log::info('Family member references count: ' . $familyReferences);
                
            if ($familyReferences > 0) {
                Log::info('Beneficiary has family member references, cannot delete');
                return [
                    'success' => false,
                    'message' => "This beneficiary is linked to {$familyReferences} family member(s) in the system. Please remove these relationships before proceeding with deletion.",
                    'error_type' => 'dependency_family'
                ];
            }
            
            // 2. Check for weekly care plans
            $weeklyCarePlans = DB::table('weekly_care_plans')
                ->where('beneficiary_id', $id)
                ->count();
            
            Log::info('Weekly care plans count: ' . $weeklyCarePlans);
                
            if ($weeklyCarePlans > 0) {
                Log::info('Beneficiary has weekly care plans, cannot delete');
                return [
                    'success' => false,
                    'message' => "This beneficiary has {$weeklyCarePlans} weekly care plan(s). These records are important for audit and historical purposes and cannot be deleted.",
                    'error_type' => 'dependency_weekly_care_plans'
                ];
            }
            
            // Get the general care plan ID from the beneficiary record
            $generalCarePlanId = $beneficiary->general_care_plan_id;      
            
            // Begin database transaction
            DB::beginTransaction();

            try {
                // First NULL the general_care_plan_id in the beneficiary record
                // This breaks the reference to allow deletion of the care plan
                DB::table('beneficiaries')
                    ->where('beneficiary_id', $id)
                    ->update(['general_care_plan_id' => null]);
                    
                // Now it's safe to delete the general care plan
                if ($generalCarePlanId) {
                    Log::info('Deleting associated general care plan ID: ' . $generalCarePlanId);
                    
                    // Delete related care needs
                    DB::table('care_needs')
                        ->where('general_care_plan_id', $generalCarePlanId)
                        ->delete();
                    
                    // Delete related care worker responsibilities
                    DB::table('care_worker_responsibilities')
                        ->where('general_care_plan_id', $generalCarePlanId)
                        ->delete();
                    
                    // Delete related medications
                    DB::table('medications')
                        ->where('general_care_plan_id', $generalCarePlanId)
                        ->delete();
                    
                    // Delete related health history
                    DB::table('health_histories')
                        ->where('general_care_plan_id', $generalCarePlanId)
                        ->delete();
                    
                    // Delete related mobility
                    DB::table('mobility')
                        ->where('general_care_plan_id', $generalCarePlanId)
                        ->delete();
                    
                    // Delete related cognitive function
                    DB::table('cognitive_function')
                        ->where('general_care_plan_id', $generalCarePlanId)
                        ->delete();
                    
                    // Delete related emotional wellbeing
                    DB::table('emotional_wellbeing')
                        ->where('general_care_plan_id', $generalCarePlanId)
                        ->delete();
                    
                    // Finally delete the general care plan itself
                    DB::table('general_care_plans')
                        ->where('general_care_plan_id', $generalCarePlanId)
                        ->delete();
                }
                
                // Then delete the beneficiary
                $result = DB::table('beneficiaries')
                    ->where('beneficiary_id', $id)
                    ->delete();
                
                Log::info('Delete result: ' . ($result ? 'success' : 'failed'));
                
                if (!$result) {
                    throw new Exception('Failed to delete beneficiary');
                }
                
                // If we got here, everything succeeded
                DB::commit();
                Log::info('Beneficiary deleted successfully: ' . $id);
                
                return [
                    'success' => true,
                    'message' => 'Beneficiary deleted successfully.',
                    'deleted_care_plan' => $generalCarePlanId ? true : false
                ];
                
            } catch (Exception $innerException) {
                // If anything fails, roll back the transaction
                DB::rollBack();
                Log::error('Inner exception while deleting beneficiary: ' . $innerException->getMessage());
                Log::error('Stack trace: ' . $innerException->getTraceAsString());
                throw $innerException;
            }
            
        } catch (QueryException $e) {
            Log::error('Database error deleting beneficiary: ' . $e->getMessage());
            Log::error('Error code: ' . $e->getCode());
            
            // Check for foreign key constraint violation
            if (in_array($e->getCode(), ['23000', '23503']) || 
                (is_numeric($e->getCode()) && in_array((int)$e->getCode(), [23000, 23503]))) {
                
                // Try to determine which table has the dependency
                $errorMsg = $e->getMessage();
                Log::error('Full error message: ' . $errorMsg);
                
                if (stripos($errorMsg, 'family_members') !== false) {
                    return [
                        'success' => false,
                        'message' => 'This beneficiary is linked to family members in the system. Please remove these relationships before proceeding with deletion.',
                        'error_type' => 'dependency_family'
                    ];
                } else if (stripos($errorMsg, 'weekly_care_plans') !== false) {
                    return [
                        'success' => false,
                        'message' => 'This beneficiary has weekly care plans that cannot be deleted due to audit and record-keeping requirements.',
                        'error_type' => 'dependency_weekly_care_plans'
                    ];
                }
                
                //Generic dependency message for other tables (for future dependencies)
                return [
                    'success' => false,
                    'message' => 'This beneficiary has records in the system that require audit history to be maintained.',
                    'error_type' => 'dependency_audit'
                ];
            }
            
            // If in development, return detailed error
            if (app()->environment('local', 'development')) {
                return [
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage(),
                    'error_type' => 'database',
                    'code' => $e->getCode()
                ];
            }
            
            return [
                'success' => false,
                'message' => 'A database error occurred. Please try again later or contact the system administrator.',
                'error_type' => 'database'
            ];
        } catch (Exception $e) {
            Log::error('General error deleting beneficiary: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // If in development, return detailed error
            if (app()->environment('local', 'development')) {
                return [
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'error_type' => 'general',
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ];
            }
            
            return [
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error_type' => 'general'
            ];
        }
    }
}