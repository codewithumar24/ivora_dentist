<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

/**
 * Adds the media settings the admin screen was always able to render but that
 * nothing ever created.
 *
 * The settings page has handled `type = image` since day one, and the upload
 * handler stores files fine — but no row of that type existed, so the fields
 * never appeared and the home page went on serving a hard-coded file. This
 * inserts the rows; the views read them with a fallback to the bundled asset,
 * so an empty value keeps the current look until someone uploads something.
 */
return new class extends Migration
{
    /** key => [value, type, group, label] */
    private array $media = [
        'logo'         => ['', 'image', 'brand',  'Logo (header and emails)'],
        'logo_light'   => ['', 'image', 'brand',  'Logo for dark backgrounds'],
        'favicon'      => ['', 'image', 'brand',  'Favicon'],
        'hero_image'   => ['', 'image', 'home',   'Home hero photograph'],
        'hero_video'   => ['', 'video', 'home',   'Home hero background video (mp4)'],
        'hero_poster'  => ['', 'image', 'home',   'Still shown before the video plays'],
        'about_image'  => ['', 'image', 'home',   'About section photograph'],
        'cta_image'    => ['', 'image', 'home',   'Closing call-to-action photograph'],
        'og_image'     => ['', 'image', 'seo',    'Social sharing image'],
    ];

    public function up(): void
    {
        foreach ($this->media as $key => [$value, $type, $group, $label]) {
            // firstOrCreate, not updateOrCreate: never blank a value the clinic
            // has already uploaded if this migration is ever re-run.
            Setting::firstOrCreate(
                ['key' => $key],
                compact('value', 'type', 'group', 'label')
            );
        }
    }

    public function down(): void
    {
        Setting::whereIn('key', array_keys($this->media))->delete();
    }
};
