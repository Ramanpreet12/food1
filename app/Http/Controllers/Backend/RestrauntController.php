<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Restaurant;
use App\Http\Requests\RestaurantRequest;

class RestrauntController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.restraunts.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.restraunts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     try {
    //         $data = [];
    //         if ($request->hasFile('logo')) {
    //             $data['logo'] = store_image('logo', 'app/public/restraunt/images/');
    //         }
    //         dd($data);
    //         // $data['homepage_section_id'] = $request->homepage_section_id;
    //         // $data['link_url'] = $request->link_url;
    //         // $data['alt_text'] = $request->alt_text;
    //         // $data['display_order'] = $request->display_order;
    //         // $data['status'] =1;

    //         // Banner::create($data);
    //         return redirect()->route('admin.banners.index')->with('success', 'Banners created successfully');
    //     } catch (Exception $e) {
    //         return redirect()->back()->with('error', 'Something went Wrong! Please Try Again.' . $e->getMessage());
    //     }
    // }


    // public function store(Request $request)
    // {
    //     try {
    //         // $request->validate([
    //         //     'name' => 'required|string|max:255',
    //         //     'description' => 'required',
    //         //     'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         //     'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         //     'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    //         // ]);

    //         // $data = $request->except(['logo', 'featured_image', 'images']);

    //         // Handle logo upload
    //         if ($request->hasFile('logo')) {
    //             $logoSizes = [
    //                 'small' => [150, 150],
    //                 'medium' => [300, 300]
    //             ];
    //             $data['logo'] = store_image('logo', 'restaurants/logos', $logoSizes);
    //         }

    //         // // Handle featured image
    //         // if ($request->hasFile('featured_image')) {
    //         //     $featuredSizes = [
    //         //         'small' => [400, 300],
    //         //         'medium' => [800, 600],
    //         //         'large' => [1200, 900]
    //         //     ];
    //         //     $data['featured_image'] = store_image('featured_image', 'restaurants/featured', $featuredSizes);
    //         // }

    //         // Handle multiple images
    //         // if ($request->hasFile('images')) {
    //         //     $gallerySizes = [
    //         //         'thumbnail' => [200, 150],
    //         //         'medium' => [600, 450],
    //         //         'large' => [1024, 768]
    //         //     ];

    //         //     $galleryImages = [];
    //         //     foreach ($request->file('images') as $index => $image) {
    //         //         $imagePaths = store_image('images.' . $index, 'restaurants/gallery', $gallerySizes);
    //         //         $galleryImages[] = $imagePaths;
    //         //     }
    //         //     $data['images'] = json_encode($galleryImages);
    //         // }

    //         // $restaurant = Restaurant::create($data);

    //         // return redirect()
    //         //     ->route('admin.restaurants.index')
    //         //     ->with('success', 'Restaurant created successfully');

    //     } catch (Exception $e) {
    //         Log::error('Restaurant creation failed: ' . $e->getMessage());
    //         return redirect()
    //             ->back()
    //             ->withInput()
    //             ->with('error', 'Failed to create restaurant: ' . $e->getMessage());
    //     }
    // }


    // public function store(Request $request)
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

            if ($request->expectsJson()) {
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



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
