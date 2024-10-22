<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Restaurant;
use App\Http\Requests\RestaurantRequest;

class RestrauntController extends Controller
{
    public function store(RestaurantRequest $request)
    {



        try {
            // Store images
            try {
                $logoFilename = store_image($request->file('logo'), 'restaurants/logos');
                $restaurantImage = store_image($request->file('restraunt_images'), 'restaurants/images');
                $featuredImage = store_image($request->file('featured_img'), 'restaurants/featured');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to upload image: ' . $e->getMessage());
            }


            $restaurant = Restaurant::create([
                'name' => $request->name,
                'description' => $request->description,
                'speciality' => $request->speciality,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'delivery_radius' => $request->delivery_radius,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'phone' => $request->phone,
                'secondary_phone' => $request->secondary_phone,
                'email' => $request->email,
                'website' => $request->website,
                'owner_name' => $request->owner_name,
                'owner_contact_number' => $request->owner_contact_number,
                'owner_email' => $request->owner_email,
                'opening_time' => $request->opening_time,
                'closing_time' => $request->closing_time,
                'days_of_operation' => $request->days_of_operation,
                'delivery_fee' => $request->delivery_fee,
                'delivery_time' => $request->delivery_time,
                'average_cost_for_per_person' => $request->average_cost_for_per_person,
                'tax_gst_number' => $request->tax_gst_number,
                'business_license' => $request->business_license,
                'logo' => $logoFilename,
                'restraunt_images' => $restaurantImage,
                'featured_image' => $featuredImage,
            ]);

            // Check if request is API
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => true,
                    'message' => 'Restaurant created successfully',
                    'data' => $restaurant,
                    'images' => [
                        'logo' => asset("storage/restaurants/logos/{$logoFilename}"),
                        'restaurant_image' => asset("storage/restaurants/images/{$restaurantImage}"),
                        'featured_image' => asset("storage/restaurants/featured/{$featuredImage}")
                    ]
                ], 201);
            }

            // Web response
            // return redirect()
            //     ->route('restaurants.index')
            //     ->with('success', 'Restaurant created successfully');

            return redirect()->back()->with('success', 'Restaurant created successfully');
        } catch (\Exception $e) {
            // Delete uploaded images if restaurant creation fails
            delete_image($logoFilename, 'restaurants/logos');
            delete_image($restaurantImage, 'restaurants/images');
            delete_image($featuredImage, 'restaurants/featured');

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to create restaurant',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create restaurant: ' . $e->getMessage());
        }
    }
}
