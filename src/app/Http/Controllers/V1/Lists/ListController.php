<?php

namespace App\Http\Controllers\V1\Lists;

use App\Contracts\ListInterface;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ListController extends Controller
{
    private $lists = [
        'Sport',
        'Brand',
        'Language',
        'HotspotCategory'
    ];

    public function __construct(ListInterface $listService)
    {
        $this->listService = $listService;
    }

    public function index($list)
    {

        if (in_array($list = ucfirst($list), $this->lists))
        {
            return response()->json(['data' => app("App\\Models\\Eloquent\\Lists\\$list")->all()]);
        }

        throw new HttpException(404, "$list Not found...");
    }

    public function getSport()
    {
        return response()->json(
            ['data' => $this->listService->getAllSports()],
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    public function getBrand()
    {
        return response()->json(
            ['data' => $this->listService->getAllBrands()],
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    public function getLanguage()
    {
        return response()->json(
            ['data' => $this->listService->getAllLanguages()],
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    public function getNationality()
    {
        return response()->json(
            ['data' => $this->listService->getAllNationalities()],
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

    public function getHotspotCategory()
    {
        return response()->json(
            ['data' => $this->listService->getAllHotspotCategories()],
            Response::HTTP_OK,
            [],
            JSON_NUMERIC_CHECK
        );
    }

}
