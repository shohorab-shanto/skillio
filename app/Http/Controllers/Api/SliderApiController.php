<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;

class SliderApiController extends Controller
{
    /**
     * Get all active sliders ordered by position
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $sliders = Slider::active()
                ->ordered()
                ->get()
                ->map(function ($slider) {
                    return [
                        'image_url' => $slider->image_url,
                        'order' => $slider->order,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Sliders retrieved successfully',
                'data' => [
                    'sliders' => $sliders,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sliders',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a single slider by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $slider = Slider::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Slider retrieved successfully',
                'data' => [
                    'slider' => [
                        'id' => $slider->id,
                        'title' => $slider->title,
                        'image_url' => $slider->image_url,
                        'link' => $slider->link,
                        'description' => $slider->description,
                        'order' => $slider->order,
                        'status' => $slider->status,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Slider not found',
            ], 404);
        }
    }
}
