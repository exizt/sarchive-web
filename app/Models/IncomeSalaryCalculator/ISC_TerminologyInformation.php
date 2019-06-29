<?php

namespace App\Models\IncomeSalaryCalculator;

use Illuminate\Database\Eloquent\Model;

/**
 * 용어 사전 (실수령액 계산기)
 * @author e2xist
 *
 */
class ISC_TerminologyInformation extends Model
{
    protected $connection = 'mysql_incomesalary';
    protected $table = 'terminology_information';
}
