<?php

namespace App\Contracts\Sport;

use App\Models\Eloquent\Lists\Sport as Eloquent;
use Illuminate\Http\Request;

interface SportInterface
{
    public function save(Eloquent $sport);
    public function find($id);
    public function delete($id);
    public function updatePivotSportUser($source, $destination);
}