<?php

namespace App\Http\Controllers\Api;

use App\DTOs\UserSettingsData;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Http\Resources\SettingsResource;
use App\Services\SettingsService;

class SettingsController extends Controller
{
    public function __construct(private readonly SettingsService $settingsService)
    {
    }

    /**
     * Get current user's settings.
     */
    public function show()
    {
        $user = auth()->user();
        $settings = $this->settingsService->getOrCreateSettings($user);

        return new SettingsResource($settings);
    }

    /**
     * Update current user's settings.
     */
    public function update(UpdateSettingsRequest $request)
    {
        $user = auth()->user();
        
        $data = UserSettingsData::fromArray($request->validated());
        $settings = $this->settingsService->updateSettings($user, $data->toArray());

        return new SettingsResource($settings);
    }
}
