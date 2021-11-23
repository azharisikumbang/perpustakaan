<?php 

namespace App\Utils;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Paginator 
{
	public const OFFSET = 10;

	public static function getOrderValue(?string $orderAs = null) : string
	{	
		if (is_null($orderAs)) {
			return 'asc';
		}

		$orderAs = strtolower($orderAs);

		switch ($orderAs) {
			case 'desc':
			case 'descending':
				$orderAs = 'desc';
				break;
			
			default:
				$orderAs = 'asc';
				break;
		}

		return $orderAs;
	}

	public static function paginateByOrderAttribute(string $orderBy, ?string $orderAs = null) : \Closure
	{
		return function ($query) use ($orderBy, $orderAs) {
            return $query->orderBy(
            	$orderBy, 
            	static::getOrderValue($orderAs)
            );
        };
	}

	public static function createFromModel(Model $model, int $limit = 10, string $orderBy = 'id', string $orderAs = 'desc') : array
	{
		$paginated = $model
			->when(true, static::paginateByOrderAttribute($orderBy, $orderAs))->paginate($limit);
		$paginated->appends(['limit' => $paginated->perPage(), 'order_by' => $orderBy, 'order_as' => $orderAs]);

		$items = $paginated->getCollection();

		return array_merge($paginated->toArray(), ['data' => $items]);
	}
}