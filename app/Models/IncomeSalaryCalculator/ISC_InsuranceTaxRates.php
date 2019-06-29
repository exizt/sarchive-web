<?php

namespace App\Models\IncomeSalaryCalculator;

use Illuminate\Database\Eloquent\Model;

/**
 * 세율 정보 (실수령액 계산기)
 * @author e2xist
 *
 */
class ISC_InsuranceTaxRates extends Model
{
    protected $connection = 'mysql_incomesalary';
    protected $table = 'insurance_tax_rates';
    
    
    public static function getRates($yearmonth)
    {
    	return self::where('yearmonth','<=',$yearmonth)->orderBy('yearmonth','desc')->first();
    }
    
    public static function getNowRates(){
    	return self::whereRaw('yearmonth <= date_format(curdate(),\'%Y%m\')')->orderBy('yearmonth','desc')->first();
    }
}
