<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function landing()
    {
        try {
            $plans = \App\Models\SubscriptionPlan::active()->get();
        } catch (\Throwable $e) {
            $plans = collect();
        }
        return view('landing', compact('plans'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function faq()
    {
        return view('public.faq');
    }

    public function features()
    {
        return view('public.features');
    }

    public function privacy()
    {
        return view('public.privacy');
    }

    public function contactStore(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
        ]);

        // Log contact submission (mail can be configured later)
        \Log::info('Contact form submission', $request->only('name', 'email', 'subject', 'message'));

        return back()->with('success', 'Thank you for reaching out! We\'ll get back to you within 24 hours.');
    }
}
