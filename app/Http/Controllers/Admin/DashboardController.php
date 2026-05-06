<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalEntities = Entity::query()->count();
        $accommodationCount = Entity::query()
            ->whereHas('entityType', fn ($q) => $q->where('code', 'accommodation'))
            ->count();
        $foodPlaceCount = Entity::query()
            ->whereHas('entityType', fn ($q) => $q->where('code', 'food_place'))
            ->count();
        $attractionCount = Entity::query()
            ->whereHas('entityType', fn ($q) => $q->where('code', 'attraction'))
            ->count();
        $withoutOwnerCount = Entity::query()->whereNull('user_id')->count();

        $recentEntities = Entity::query()
            ->with(['entityType', 'place', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'totalEntities' => $totalEntities,
            'accommodationCount' => $accommodationCount,
            'foodPlaceCount' => $foodPlaceCount,
            'attractionCount' => $attractionCount,
            'withoutOwnerCount' => $withoutOwnerCount,
            'recentEntities' => $recentEntities,
        ]);
    }
}

