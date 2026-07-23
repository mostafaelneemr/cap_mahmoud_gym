<?php

namespace App\Repositories\Exercise;

use App\Models\Exercise;
use App\Repositories\BaseRepository;

class ExerciseRepository extends BaseRepository
{
    protected $modeler = Exercise::class;

}
