<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqTableSeeder extends Seeder
{
    public function run()
    {
        Faq::truncate();

        $faqs = [
            ['How do I log in to the admin panel?', 'Visit /bp-admin and sign in with your administrator account.', 1],
            ['Does Beyond Plus CMS support multiple languages?', 'Yes — content and menus can be translated, and the front-end serves the matching locale automatically.', 2],
            ['How do I add a new page or post?', 'From the admin panel open Post or Page, click Add, then fill in the title and content.', 3],
            ['Can I use my own theme?', 'Yes. Themes live in resources/views/theme/<name>/ and can be switched from the Themes admin page.', 4],
        ];

        foreach ($faqs as [$question, $answer, $order]) {
            Faq::create(['question' => $question, 'answer' => $answer, 'sort_order' => $order, 'is_active' => 1]);
        }
    }
}
