<?php

use Illuminate\Database\Seeder;
use App\Company;

class CompaniesUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companyIDs = Company::pluck('id');
        foreach($companyIDs as $id)
        {
            $company = Company::find($id);
            $company->slug = null;
            $company->save();
        }
    }
}
