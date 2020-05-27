<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Location;
use App\Node;
use Faker\Generator as Faker;

$factory->define(Node::class, function (Faker $faker) {
	return [
		'type'      => 'ogp',
		'hostname'  => 'https://' . $faker->word . '.denerdtv.com/',
		'location_id' => Location::query()->inRandomOrder()->first(),
	];
});
