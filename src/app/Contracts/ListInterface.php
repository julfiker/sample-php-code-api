<?php

namespace App\Contracts;

use App\Http\Requests\Request;
use App\Models\Eloquent\Activity\Activity as Eloquent;

interface ListInterface
{
    public function getAllSports();
    public function getAllBrands();
    public function getAllLanguages();
    public function getAllNationalities();
    public function getAllHotspotCategories();
}