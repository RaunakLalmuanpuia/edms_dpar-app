<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Agriculture Department and Farmers Welfare Department',
            'Animal Husbandry and Veterinary Department',
            'Art and Culture Department',
            'Commerce and Industries Department',
            'Co-operation Department',
            'Disaster Management and Rehabilitation Department',
            'District Council and Minority Affairs Department',
            'Environment, Forests and Climate Change Department',
            'Excise and Narcotics Department',
            'Finance Department',
            'Fisheries Department',
            'Food, Civil Supplies and Consumer Affairs Department',
            'General Administration Department',
            'Health and Family Welfare Department',
            'Higher and Technical Education Department',
            'Home Department',
            'Horticulture Department',
            'Information and Communication Technology Department',
            'Information, Public Relations, Printing and Stationery Department',
            'Irrigation and Water Resources Department',
            'Labour, Employment, Skill Development and Entrepreneurship Department',
            'Land Resources, Soil and Water Conservation Department',
            'Land Revenue and Settlement Department',
            'Law and Judicial Department',
            'Parliamentary Affairs Department',
            'Personnel and Administrative Reforms Department',
            'Planning and Programme Implementation Department',
            'Political and Cabinet Department',
            'Power and Electricity Department',
            'Public Health Engineering Department',
            'Public Works Department',
            'Rural Development and Administration Department',
            'School Education Department',
            'Sericulture Department',
            'Social Welfare, Women and Child Development Department',
            'Sport and Youth Services Department',
            'Tourism Department',
            'Transport Department',
            'Urban Development and Poverty Alleviation Department',
            'Vigilance Department',
        ];

        $data = [];

        foreach ($departments as $dept) {
            $data[] = [
                'name' => $dept,
                'type' => 'Department',
                'location' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('offices')->insert($data);
    }
}
