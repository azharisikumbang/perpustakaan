<?php 

namespace App\Models;

trait SearchableTrait
{
	public function getSearchableTypeAttribute() : string
	{
		return basename(__CLASS__);
	}
}