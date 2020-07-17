<?php

namespace App\Http\Controllers\V1\Hotspot;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hotspot\CreateHotspotRequest;
use App\Http\Requests\Hotspot\UpdateHotspotRequest;
use App\Jobs\Hotspot\CreateHotspotJob;
use App\Jobs\Hotspot\UpdateHotspotJob;
use App\Jobs\Hotspot\DeleteHotspotJob;
use App\Repositories\Hotspot\HotspotRepository;
use Illuminate\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HotspotController extends Controller
{
    public $hotspot;
    public $auth;

    public function __construct(HotspotRepository $hotspotRepository, Guard $auth)
    {
        $this->hotspot = $hotspotRepository;
        $this->auth = $auth;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(
            ['data' => $this->hotspot->search(['user_id' => $this->auth->user()->id ])],
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param CreateHotspotRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(CreateHotspotRequest $request, Guard $auth)
    {
        $hotspot = $this->dispatchFrom(CreateHotspotJob::class, $request, ['user_id' => $auth->user()->id]);
        return response()->json(
            ['data' => $hotspot],
            Response::HTTP_CREATED,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateHotspotRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHotspotRequest $request, $id)
    {
        $this->dispatchFrom(UpdateHotspotJob::class, $request, array('id' => $id, 'user_id' => $this->auth->user()->id));
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->dispatchFrom(DeleteHotspotJob::class, collect(array('id' => $id)), ['user_id' => $this->auth->user()->id]);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
