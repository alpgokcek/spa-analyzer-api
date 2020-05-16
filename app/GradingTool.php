<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GradingTool extends Model
{
	protected $table = 'grading_tool';
	protected $fillable = [];

	protected $casts = [
			'created_at' => 'date:d-m-Y',
			'updated_at' => 'date:d-m-Y'
	];
}
