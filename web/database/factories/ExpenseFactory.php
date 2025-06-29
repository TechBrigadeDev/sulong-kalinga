<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    protected static $adminIds = null;
    protected static $categoryIds = null;
    protected static $expenseTemplates = [
        'Medical Supplies' => [
            ['title' => 'Purchase of wound dressings', 'description' => 'Procurement of sterile gauze, bandages, and wound care kits for beneficiaries.'],
            ['title' => 'Acquisition of PPE', 'description' => 'Purchase of masks, gloves, and protective equipment for staff and volunteers.'],
        ],
        'Medications' => [
            ['title' => 'Monthly maintenance medicines', 'description' => 'Bulk purchase of maintenance medicines for elderly beneficiaries.'],
            ['title' => 'Antibiotic procurement', 'description' => 'Purchase of antibiotics for infection control and treatment.'],
        ],
        'Food & Nutrition' => [
            ['title' => 'Weekly food packs', 'description' => 'Distribution of nutritious food packs to undernourished beneficiaries.'],
            ['title' => 'Supplemental feeding', 'description' => 'Provision of supplemental feeding for malnourished elderly.'],
        ],
        'Transportation/Fuel' => [
            ['title' => 'Fuel for outreach', 'description' => 'Gasoline expenses for vehicles used in community outreach programs.'],
            ['title' => 'Transport for medical appointments', 'description' => 'Transportation costs for bringing beneficiaries to hospitals and clinics.'],
        ],
        'Facility Maintenance' => [
            ['title' => 'Plumbing repairs', 'description' => 'Repair and maintenance of water pipes and restrooms in the facility.'],
            ['title' => 'General cleaning', 'description' => 'Deep cleaning and sanitation of the main office and beneficiary areas.'],
        ],
        'Staff Training' => [
            ['title' => 'First aid seminar', 'description' => 'Staff and volunteer training on basic first aid and emergency response.'],
            ['title' => 'Caregiver skills workshop', 'description' => 'Workshop for care workers on elderly care best practices.'],
        ],
        'Administrative' => [
            ['title' => 'Office supplies', 'description' => 'Purchase of paper, pens, and other administrative materials.'],
            ['title' => 'Document notarization', 'description' => 'Legal and documentation expenses for program compliance.'],
        ],
        'Program Activities' => [
            ['title' => 'Wellness session', 'description' => 'Conducting wellness and exercise sessions for beneficiaries.'],
            ['title' => 'Arts and crafts activity', 'description' => 'Materials for arts and crafts sessions for elderly engagement.'],
        ],
        'Community Outreach' => [
            ['title' => 'Barangay health mission', 'description' => 'Expenses for medical missions in local barangays.'],
            ['title' => 'Information drive', 'description' => 'Printing and distribution of flyers for health awareness campaigns.'],
        ],
        'Emergency Response' => [
            ['title' => 'Typhoon relief', 'description' => 'Emergency food and supply distribution for typhoon-affected families.'],
            ['title' => 'Medical emergency fund', 'description' => 'Immediate fund for urgent medical needs of beneficiaries.'],
        ],
        'Other' => [
            ['title' => 'Miscellaneous expense', 'description' => 'Uncategorized expense for unforeseen needs.'],
            ['title' => 'Contingency fund', 'description' => 'Reserve fund for unexpected operational requirements.'],
        ],
    ];

    public function definition()
    {
        // Cache admin IDs
        if (is_null(self::$adminIds)) {
            self::$adminIds = User::where('role_id', 1)->pluck('id')->toArray();
        }
        $adminId = !empty(self::$adminIds) ? $this->faker->randomElement(self::$adminIds) : User::factory();

        // Cache category IDs and names
        if (is_null(self::$categoryIds)) {
            self::$categoryIds = ExpenseCategory::pluck('category_id', 'name')->toArray();
        }
        $categoryNames = array_keys(self::$categoryIds);
        $categoryName = $this->faker->randomElement($categoryNames);
        $categoryId = self::$categoryIds[$categoryName];

        // Pick a template for this category
        $templates = self::$expenseTemplates[$categoryName] ?? self::$expenseTemplates['Other'];
        $entry = $this->faker->randomElement($templates);

        $paymentMethods = ['cash', 'check', 'bank_transfer', 'gcash', 'paymaya', 'credit_card', 'debit_card', 'other'];
        $randomDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $receiptNumber = 'RCPT-' . strtoupper(uniqid());

        return [
            'category_id' => $categoryId,
            'title' => $entry['title'],
            'description' => $entry['description'],
            'amount' => $this->faker->numberBetween(500, 10000),
            'payment_method' => $this->faker->randomElement($paymentMethods),
            'date' => $randomDate->format('Y-m-d'),
            'receipt_number' => $receiptNumber,
            'receipt_path' => null,
            'created_by' => $adminId,
            'updated_by' => $adminId,
            'created_at' => $randomDate,
            'updated_at' => now(),
        ];
    }
}