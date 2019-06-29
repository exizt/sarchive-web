<?php

namespace App\Models\IncomeSalaryCalculator;

use Illuminate\Database\Eloquent\Model;

class ISC_IncomeTaxTable extends Model
{
    protected $connection = 'mysql_incomesalary';
    protected $table = 'income_tax_table';
}
