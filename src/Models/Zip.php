<?php

namespace Bendt\Zip\Models;

class Zip extends BaseModel {

	protected $table = 'zip';

	public $timestamps = false;

	protected $files = [];

}