<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SiteRequest;
use App\Http\Resources\SiteResource;
use App\Services\SiteService;
use Exception;
use App\Traits\ApiRequestTrait;

class SiteController extends AdminController
{
    use ApiRequestTrait;
    protected $apiRequest;
    public SiteService $siteService;

    public function __construct(SiteService $siteService)
    {
        parent::__construct();
        $this->apiRequest = $this->makeApiRequest();
        $this->siteService = $siteService;
        $this->middleware(['permission:settings'])->only('update');
    }

    public function index(): SiteResource|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new SiteResource($this->siteService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(SiteRequest $request): SiteResource|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if (env('DEMO')) {
                return new SiteResource($this->siteService->update($request));
            } else {
                if ($this->apiRequest->status) {
                    return new SiteResource($this->siteService->update($request));
                }
                return response(['status' => false, 'message' => $this->apiRequest->message], 422);
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
