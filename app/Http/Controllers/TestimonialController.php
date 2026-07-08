<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Support\TestimonialDisplayName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TestimonialController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        if ($request->filled('company_website')) {
            return redirect()
                ->to(route('home').'#destekci-deneyimleri')
                ->with('testimonial_success', true);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:20', 'max:500'],
            'is_anonymous' => ['sometimes', 'boolean'],
            'is_volunteer' => ['sometimes', 'boolean'],
            'is_donor' => ['sometimes', 'boolean'],
            'kvkk_consent' => ['accepted'],
        ], [
            'name.required' => __('app.home.testimonials_validation_name'),
            'city.required' => __('app.home.testimonials_validation_city'),
            'email.required' => __('app.home.testimonials_validation_email'),
            'email.email' => __('app.home.testimonials_validation_email_format'),
            'rating.required' => __('app.home.testimonials_validation_rating'),
            'comment.required' => __('app.home.testimonials_validation_comment'),
            'comment.min' => __('app.home.testimonials_validation_comment_min'),
            'kvkk_consent.accepted' => __('app.home.testimonials_validation_kvkk'),
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to(route('home').'#destekci-deneyimleri')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $isAnonymous = $request->boolean('is_anonymous');

        Testimonial::query()->create([
            'name' => $validated['name'],
            'display_name' => TestimonialDisplayName::make($validated['name'], $isAnonymous),
            'city' => $validated['city'],
            'email' => $validated['email'],
            'rating' => (int) $validated['rating'],
            'comment' => Str::of(strip_tags($validated['comment']))->squish()->toString(),
            'is_anonymous' => $isAnonymous,
            'is_volunteer' => $request->boolean('is_volunteer'),
            'is_donor' => $request->boolean('is_donor'),
            'status' => Testimonial::STATUS_PENDING,
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->to(route('home').'#destekci-deneyimleri')
            ->with('testimonial_success', true);
    }
}
