<?php

namespace App\Services;

use Cache;
use App\Profile;
use App\Transformer\Api\AccountTransformer;
use League\Fractal;
use League\Fractal\Serializer\ArraySerializer;

class AccountService {

	const CACHE_KEY = 'pf:services:account:';

	public static function get($id)
	{
		if($id > PHP_INT_MAX || $id < 1) {
			return [];
		}

		$key = self::CACHE_KEY . $id;
		$ttl = now()->addMinutes(15);

		return Cache::remember($key, $ttl, function() use($id) {
			$fractal = new Fractal\Manager();
			$fractal->setSerializer(new ArraySerializer());
			$profile = Profile::whereNull('status')->findOrFail(intval($id));
			$resource = new Fractal\Resource\Item($profile, new AccountTransformer());
			return $fractal->createData($resource)->toArray();
		});	
	}

	public static function del($id)
	{
		return Cache::forget(self::CACHE_KEY . $id);
	}

}