<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\Office;
use App\Models\RemunerationDetail;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $offices = Office::all();

        // Document Types
        $docTypes = collect([
            'Aadhar',
            'EPIC',
            'Birth Certificate',
            'Educational Certificate',
            'Technical Certificate',
        ])->map(fn($type) => DocumentType::create([
            'name' => $type,
            'description' => "$type document",
        ]));

        // Predefined
        $designations = [
            'Junior Assistant','Senior Assistant','Supervisor','Manager','Clerk',
            'Field Officer','Technical Assistant','Engineer','Accountant','Data Entry Operator'
        ];

        $mrSkills = ['Unskilled', 'Semi-Skilled', 'Skilled-I', 'Skilled-II'];

        $educationalQualifications = ['U/M','HSLC','HSSLC','Graduate & Level','Master Degree & Level'];
        $technicalQualifications = [
            'Diploma in IT','Certificate in Electricals','B.Tech in Civil Engineering',
            'B.Sc in Computer Science','ITI Welder','None'
        ];

        // Create employees for all offices
        $employeesPerOffice = 5;
        $employeeCounter = 1;
        $allEmployees = collect();

        foreach ($offices as $office) {

            // Define exact counts per type
            $employmentTypeCounts = [
                'MR' => 5,
                'PE' => 5,
                'WC' => 5,
            ];

            foreach ($employmentTypeCounts as $employmentType => $count) {

                for ($i = 0; $i < $count; $i++) {

                    $designationValue = collect($designations)->random();

                    // Generate DOB once for retirement calculation
                    $dob = fake()->date('Y-m-d', '2000-01-01');

                    $employee = Employee::create([
                        'office_id'              => $office->id,
                        'employee_code'          => 'EMP' . str_pad($employeeCounter, 4, '0', STR_PAD_LEFT),
                        'name'                   => fake()->name(),
                        'mobile'                 => fake()->unique()->numerify('9#########'),
                        'email'                  => fake()->unique()->safeEmail(),
                        'address'                => fake()->address(),
                        'date_of_birth'          => $dob,
                        'parent_name'            => fake()->name(),
                        'employment_type'        => $employmentType,
                        'educational_qln'        => collect($educationalQualifications)->random(),
                        'technical_qln'          => collect($technicalQualifications)->random(),

                        // PE + WC have designation
                        'designation'            => in_array($employmentType, ['PE', 'WC'])
                            ? $designationValue
                            : null,

                        // MR employees use post_assigned
                        'post_assigned'          => $employmentType === 'MR'
                            ? $designationValue
                            : null,

                        'name_of_workplace'      => $office->name,
                        'post_per_qualification' => collect($designations)->random(),
                        'date_of_engagement'     => fake()->date(),

                        'skill_category'         => $employmentType === 'MR'
                            ? collect($mrSkills)->random()
                            : null,

                        'skill_at_present'       => $employmentType === 'MR'
                            ? collect($mrSkills)->random()
                            : null,

                        // WC retirement at 60
                        'date_of_retirement'     => $employmentType === 'WC'
                            ? Carbon::parse($dob)->addYears(60)->toDateString()
                            : null,
                    ]);

                    $allEmployees->push($employee);
                    $employeeCounter++;
                }
            }
        }
        // Pay Matrix Levels
        $payMatrixLevels = [
            'Level 1 (17,400 - 38,600)' => [17400, 38600],
            'Level 2 (19,900 - 44,400)' => [19900, 44400],
            'Level 3 (21,700 - 48,500)' => [21700, 48500],
            'Level 4 (25,500 - 56,800)' => [25500, 56800],
            'Level 5 (29,200 - 64,700)' => [29200, 64700],
        ];

        // Remuneration for PE and WC
        $allEmployees->each(function ($employee) use ($payMatrixLevels) {

            if (in_array($employee->employment_type, ['PE', 'WC'])) {

                // Generate remuneration within pay matrix ranges
                $remuneration = fake()->numberBetween(17400, 64700);

                // Determine Pay Matrix Level
                $payMatrix = null;
                foreach ($payMatrixLevels as $level => [$min, $max]) {
                    if ($remuneration >= $min && $remuneration <= $max) {
                        $payMatrix = $level;
                        break;
                    }
                }

                RemunerationDetail::create([
                    'employee_id'         => $employee->id,
                    'remuneration'        => $remuneration,
                    'pay_matrix'          => $payMatrix,
                    'next_increment_date' => now()->addYear()->toDateString(),
                ]);
            }
        });

        /**
         * ---------------------------------------------------------
         * TRANSFER HISTORY FOR ALL EMPLOYEES
         * ---------------------------------------------------------
         */
        $allEmployees->each(function ($employee) use ($offices) {

            $availableOffices = $offices->pluck('id')->toArray();
            $currentOfficeId = $employee->office_id;

            $transferCount = rand(1, 3);
            $transferDate = Carbon::now()->subYears(3);

            for ($i = 0; $i < $transferCount; $i++) {

                $newOfficeId = collect($availableOffices)
                    ->reject(fn($id) => $id === $currentOfficeId)
                    ->random();

                Transfer::create([
                    'employee_id'   => $employee->id,
                    'old_office_id' => $currentOfficeId,
                    'new_office_id' => $newOfficeId,
                    'transfer_date' => $transferDate->copy()->addMonths($i * rand(6, 12))->toDateString(),
                    'supporting_document'=> 'uploads/transfers/' . fake()->uuid() . '.pdf',
                ]);

                $currentOfficeId = $newOfficeId;
            }

            $employee->update(['office_id' => $currentOfficeId]);
        });
    }
}
