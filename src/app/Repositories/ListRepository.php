<?php

namespace App\Repositories;


use App\Contracts\ListInterface;
use App\Models\Eloquent\Lists\Brand;
use App\Models\Eloquent\Lists\HotspotCategory;
use App\Models\Eloquent\Lists\Language;
use App\Models\Eloquent\Lists\Nationality;
use App\Models\Eloquent\Lists\Sport;
use Illuminate\Support\Facades\Config;

class ListRepository implements ListInterface  {

    public function __construct(
        Sport $sportEloquent,
        Brand $brandEloquent,
        Language $languageEloquent,
        Nationality $nationalityEloquent,
        HotspotCategory $hotspotCategoryEloquent
    )
    {
        $this->sportEloquent = $sportEloquent;
        $this->brandEloquent = $brandEloquent;
        $this->languageEloquent = $languageEloquent;
        $this->nationalityEloquent = $nationalityEloquent;
        $this->hotspotCategoryEloquent = $hotspotCategoryEloquent;
        $this->pageSize = Config::get('constants.page_size');
    }

    public function getAllSports()
    {
        return $this->sportEloquent
            ->orderBy('name', 'asc')
            ->get()
            ;
    }

    public function getAllBrands()
    {
        return $this->brandEloquent
            ->orderBy('name', 'asc')
            ->get()
            ;
    }

    public function getAllLanguages()
    {
        return $this->languageEloquent
            ->orderBy('name', 'asc')
            ->get()
            ;
    }

    public function getAllNationalities()
    {
        return $this->nationalityEloquent
            ->orderBy('name', 'asc')
            ->get()
            ;
    }

    public function getAllHotspotCategories()
    {
        return $this->hotspotCategoryEloquent
            ->orderBy('id', 'asc')
            ->get()
            ;
    }
}