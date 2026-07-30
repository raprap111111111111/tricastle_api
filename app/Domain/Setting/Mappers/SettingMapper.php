<?php

// app/Domain/Setting/Mappers/SettingMapper.php

namespace App\Domain\Setting\Mappers;

use App\Domain\Setting\DTOs\CreateSettingDTO;
use App\Domain\Setting\DTOs\UpdateSettingDTO;
use App\Http\Requests\v1\Setting\StoreSettingRequest;
use App\Http\Requests\v1\Setting\UpdateSettingRequest;

class SettingMapper
{
    public static function fromStoreRequest(StoreSettingRequest $request): CreateSettingDTO
    {
        return new CreateSettingDTO(
            key:         $request->validated('key'),
            value:       $request->validated('value'),
            type:        $request->validated('type', 'string'),
            group:       $request->validated('group', 'general'),
            description: $request->validated('description'),
            isPublic:    $request->validated('is_public', false),
        );
    }

    public static function fromUpdateRequest(UpdateSettingRequest $request): UpdateSettingDTO
    {
        return new UpdateSettingDTO(
            key:         $request->validated('key'),
            value:       $request->validated('value'),
            type:        $request->validated('type'),
            group:       $request->validated('group'),
            description: $request->validated('description'),
            isPublic:    $request->validated('is_public'),
        );
    }
}