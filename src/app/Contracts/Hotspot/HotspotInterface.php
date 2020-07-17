<?php

namespace App\Contracts\Hotspot;


use App\Models\Eloquent\Hotspot\Hotspot;

interface HotspotInterface
{
    public function save(Hotspot $hotspot);
    public function find($id);
    public function search($conditions);
    public function delete($id);
}