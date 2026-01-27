<?php

namespace App\Http\Controllers;

use App\Models\AdminLandingPage;

class FlowLandingController extends Controller
{
    /**
     * Display the main Flow landing page (slug: "flow" or first active)
     */
    public function index()
    {
        // Try to find 'flow' slug first, otherwise get first active landing page
        $landingPage = AdminLandingPage::where('slug', 'flow')
            ->where('is_active', true)
            ->first();

        if (! $landingPage) {
            $landingPage = AdminLandingPage::where('is_active', true)
                ->first();
        }

        if (! $landingPage) {
            abort(404, 'Landing page tidak ditemukan.');
        }

        return $this->renderLandingPage($landingPage);
    }

    /**
     * Display a landing page by slug
     */
    public function show(string $slug)
    {
        $landingPage = AdminLandingPage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return $this->renderLandingPage($landingPage);
    }

    /**
     * Render the landing page view
     */
    protected function renderLandingPage(AdminLandingPage $landingPage)
    {
        // Load all active sections with their active items, ordered
        $landingPage->load([
            'activeSections' => function ($query) {
                $query->with(['activeItems']);
            },
            'cta',
        ]);

        // Organize sections by key for easier access in view
        $sections = [];
        foreach ($landingPage->activeSections as $section) {
            $sections[$section->section_key] = $section;
        }

        return view('flow.landing', [
            'landingPage' => $landingPage,
            'sections' => $sections,
            'cta' => $landingPage->cta,
        ]);
    }
}
