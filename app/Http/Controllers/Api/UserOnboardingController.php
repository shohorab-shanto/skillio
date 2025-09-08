<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\UserPreference;
use App\Helpers\CountryList;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserOnboardingController extends Controller
{
    /**
     * Get all categories with subcategories
     */
    public function getCategories(): JsonResponse
    {
        try {
            $categories = Category::with('subCategories')->get();

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => [
                    'categories' => $categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'description' => $category->description,
                            'image_url' => $category->hasValidImage() ? $category->image_url : null,
                            'sub_categories' => $category->subCategories->map(function ($subCategory) {
                                return [
                                    'id' => $subCategory->id,
                                    'name' => $subCategory->name,
                                    'description' => $subCategory->description,
                                    'is_custom' => $subCategory->is_custom,
                                    'created_by_user_id' => $subCategory->created_by_user_id,
                                    'created_by_user_name' => $subCategory->createdByUser ? $subCategory->createdByUser->name : null,
                                ];
                            }),
                        ];
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'errors' => ['server' => 'An error occurred while fetching categories'],
            ], 500);
        }
    }

    /**
     * Get list of countries
     */
    public function getCountries(): JsonResponse
    {
        try {
            $countries = CountryList::all();

            return response()->json([
                'success' => true,
                'message' => 'Countries retrieved successfully',
                'data' => [
                    'countries' => $countries,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve countries',
                'errors' => ['server' => 'An error occurred while fetching countries'],
            ], 500);
        }
    }

    /**
     * Save category selection
     */
    public function saveCategorySelection(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => ['required', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'exists:sub_categories,id'],
            'custom_category_name' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();
            \Log::info('User in category selection: ' . ($user ? $user->id : 'null'));
            $categoryId = $request->category_id;
            $subCategoryId = $request->sub_category_id;
            $customName = $request->custom_category_name;

            // Handle custom category creation for "Others" category
            $category = Category::find($categoryId);
            if ($customName && $category && strtolower($category->name) == 'others') {
                // Check if custom subcategory already exists for this user
                $existingSubCategory = \App\Models\SubCategory::where('category_id', $categoryId)
                    ->where('name', $customName)
                    ->where('created_by_user_id', $user->id)
                    ->first();

                if ($existingSubCategory) {
                    // Update existing subcategory
                    $existingSubCategory->update([
                        'name' => $customName,
                        'description' => 'Custom subcategory (updated)',
                    ]);
                    $subCategoryId = $existingSubCategory->id;
                } else {
                    // Create new subcategory under "Others" category
                    $customSubCategory = \App\Models\SubCategory::create([
                        'category_id' => $categoryId,
                        'name' => $customName,
                        'description' => 'Custom subcategory',
                        'image' => null,
                        'created_by_user_id' => $user->id,
                        'is_custom' => true,
                    ]);
                    $subCategoryId = $customSubCategory->id;
                }
            }

            // Save or update user preference
            $preference = UserPreference::firstOrNew(['user_id' => $user->id]);
            $preference->category_id = $categoryId;
            if ($subCategoryId) {
                $preference->sub_category_id = $subCategoryId;
            }
            $preference->save();

            return response()->json([
                'success' => true,
                'message' => 'Category selection saved successfully',
                'data' => [
                    'category_id' => $categoryId,
                    'sub_category_id' => $subCategoryId,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Category selection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save category selection',
                'errors' => ['server' => 'An error occurred while saving category selection: ' . $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Save education type preference
     */
    public function saveEducationType(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'education_type' => ['required', 'in:in-person,online'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();
            $preference = UserPreference::firstOrNew(['user_id' => $user->id]);
            $preference->education_type = $request->education_type;
            $preference->save();

            return response()->json([
                'success' => true,
                'message' => 'Education type saved successfully',
                'data' => [
                    'education_type' => $request->education_type,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save education type',
                'errors' => ['server' => 'An error occurred while saving education type'],
            ], 500);
        }
    }

    /**
     * Save location preference (for in-person education)
     */
    public function saveLocation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'country' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();
            $preference = UserPreference::firstOrNew(['user_id' => $user->id]);
            $preference->country = $request->country;
            $preference->city = $request->city;
            $preference->save();

            return response()->json([
                'success' => true,
                'message' => 'Location saved successfully',
                'data' => [
                    'country' => $request->country,
                    'city' => $request->city,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save location',
                'errors' => ['server' => 'An error occurred while saving location'],
            ], 500);
        }
    }

    /**
     * Save online education options
     */
    public function saveOnlineOptions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'education_option' => ['required', 'in:courses,mentoring,both'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();
            $option = $request->education_option;
            $wantsCourses = $option == 'courses' || $option == 'both';
            $wantsMentoring = $option == 'mentoring' || $option == 'both';

            $preference = UserPreference::firstOrNew(['user_id' => $user->id]);
            $preference->wants_courses = $wantsCourses;
            $preference->wants_mentoring = $wantsMentoring;
            $preference->save();

            return response()->json([
                'success' => true,
                'message' => 'Online education options saved successfully',
                'data' => [
                    'education_option' => $option,
                    'wants_courses' => $wantsCourses,
                    'wants_mentoring' => $wantsMentoring,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save online education options',
                'errors' => ['server' => 'An error occurred while saving online education options'],
            ], 500);
        }
    }

    /**
     * Get onboarding status
     */
    public function getOnboardingStatus(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $preference = UserPreference::where('user_id', $user->id)->first();

            $status = [
                'category_selected' => !is_null($preference?->category_id),
                'education_type_selected' => !is_null($preference?->education_type),
                'location_selected' => !is_null($preference?->country) && !is_null($preference?->city),
                'online_options_selected' => !is_null($preference?->wants_courses) || !is_null($preference?->wants_mentoring),
            ];

            $isComplete = $status['category_selected'] && 
                         $status['education_type_selected'] && 
                         ($status['location_selected'] || $status['online_options_selected']);

            return response()->json([
                'success' => true,
                'message' => 'Onboarding status retrieved successfully',
                'data' => [
                    'status' => $status,
                    'is_complete' => $isComplete,
                    'next_step' => $this->getNextStep($status),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve onboarding status',
                'errors' => ['server' => 'An error occurred while fetching onboarding status'],
            ], 500);
        }
    }

    /**
     * Get user preferences
     */
    public function getUserPreferences(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $preference = UserPreference::where('user_id', $user->id)
                ->with(['category', 'subCategory'])
                ->first();

            if (!$preference) {
                return response()->json([
                    'success' => true,
                    'message' => 'No preferences found',
                    'data' => [
                        'preferences' => null,
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'User preferences retrieved successfully',
                'data' => [
                    'preferences' => [
                        'category' => $preference->category ? [
                            'id' => $preference->category->id,
                            'name' => $preference->category->name,
                        ] : null,
                        'sub_category' => $preference->subCategory ? [
                            'id' => $preference->subCategory->id,
                            'name' => $preference->subCategory->name,
                        ] : null,
                        'education_type' => $preference->education_type,
                        'country' => $preference->country,
                        'city' => $preference->city,
                        'wants_courses' => $preference->wants_courses,
                        'wants_mentoring' => $preference->wants_mentoring,
                        'created_at' => $preference->created_at,
                        'updated_at' => $preference->updated_at,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user preferences',
                'errors' => ['server' => 'An error occurred while fetching user preferences'],
            ], 500);
        }
    }

    /**
     * Get custom subcategories created by the current user
     */
    public function getMyCustomSubCategories(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $customSubCategories = \App\Models\SubCategory::custom()
                ->createdByUser($user->id)
                ->with('category')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Custom subcategories retrieved successfully',
                'data' => [
                    'custom_subcategories' => $customSubCategories->map(function ($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                            'description' => $subCategory->description,
                            'category' => [
                                'id' => $subCategory->category->id,
                                'name' => $subCategory->category->name,
                            ],
                            'created_at' => $subCategory->created_at,
                            'updated_at' => $subCategory->updated_at,
                        ];
                    }),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve custom subcategories',
                'errors' => ['server' => 'An error occurred while fetching custom subcategories'],
            ], 500);
        }
    }

    /**
     * Get all custom subcategories (admin view)
     */
    public function getAllCustomSubCategories(Request $request): JsonResponse
    {
        try {
            $customSubCategories = \App\Models\SubCategory::custom()
                ->with(['category', 'createdByUser'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'All custom subcategories retrieved successfully',
                'data' => [
                    'custom_subcategories' => $customSubCategories->map(function ($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                            'description' => $subCategory->description,
                            'category' => [
                                'id' => $subCategory->category->id,
                                'name' => $subCategory->category->name,
                            ],
                            'created_by_user' => [
                                'id' => $subCategory->createdByUser->id,
                                'name' => $subCategory->createdByUser->name,
                                'email' => $subCategory->createdByUser->email,
                            ],
                            'created_at' => $subCategory->created_at,
                            'updated_at' => $subCategory->updated_at,
                        ];
                    }),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve custom subcategories',
                'errors' => ['server' => 'An error occurred while fetching custom subcategories'],
            ], 500);
        }
    }

    /**
     * Determine next step in onboarding
     */
    private function getNextStep(array $status): ?string
    {
        if (!$status['category_selected']) {
            return 'category_selection';
        }
        if (!$status['education_type_selected']) {
            return 'education_type';
        }
        if ($status['education_type_selected'] && $status['education_type'] == 'in-person' && !$status['location_selected']) {
            return 'location';
        }
        if ($status['education_type_selected'] && $status['education_type'] == 'online' && !$status['online_options_selected']) {
            return 'online_options';
        }
        return null; // Onboarding complete
    }
}
