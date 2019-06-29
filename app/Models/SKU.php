<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SKU extends Model {
	//
	protected $table = 'sku_table';
	protected $fillable = [
			'prefix',
			'product_name', 
			'depth_1',
			'depth_2',
			'depth_3',
			'depth_4',
			'depth_5',
	];
	protected $appends = array (
			'product_sku' 
	);
	protected $attributes = [ 
			'depth_1' => '',
			'depth_2' => '',
			'depth_3' => '',
			'depth_4' => '',
			'depth_5' => '',
			'product_name' => '' 
	];
	public function getProductSkuAttribute() {
		return 'SHN' . $this->attributes ['depth_1'] . '-' . $this->attributes ['depth_2'] . $this->attributes ['depth_3'] . '-' . $this->attributes ['depth_4'] . $this->attributes ['depth_5'];
	}
}
