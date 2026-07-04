<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bp_module;

class ModuleTableSeeder extends Seeder
{
    /**
     * Admin modules, kept in sync with database/sample-data.sql.
     *
     * Parents are referenced by module_link (not a hard-coded id), so the
     * hierarchy stays correct no matter what order rows are inserted in — this
     * is what prevents the sidebar drift the two DB-setup paths used to have.
     *
     * @return void
     */
    public function run()
    {
        Bp_module::truncate();

        // [ name, name_mm, link, weight, icon, parent (link|null), section ]
        $modules = [
            // top-level (parents must be listed before their children)
            ['Dashboard', 'ပင်မစာမျက်နှာ', '/', 0, 'fa fa-dashboard', null, 1],
            ['Post', 'ပိုစ့်', 'post', 1, 'fa fa-edit', null, 1],
            ['Page', 'စာမျက်နှာ', 'page', 2, 'fa fa-edit', null, 1],
            ['Menu', 'အညွန်းများ', 'menu', 3, 'fa fa-table', null, 1],
            ['Media', 'မီဒီယာ', 'media', 4, 'fa fa-desktop', null, 1],
            ['Slider', 'ကြော်ငြာ', 'slider', 5, 'fa fa-image', null, 1],
            ['FAQ', 'အမေးအဖြေများ', 'faq', 6, 'fa fa-question-circle', null, 1],
            ['Feedback', 'တုံ့ပြန်ချက်များ', 'feedback', 7, 'fa fa-comments', null, 1],
            ['User Management', 'အဖွဲ့ဝင်များ', 'user', 8, 'fa fa-windows', null, 1],
            ['Settings', 'ထိန်းချုပ်ရေး', 'settings', 9, 'fa fa-bug', null, 1],
            ['Report', 'အစီရင်ခံစာ', 'reports', 10, 'fa fa-bar-chart', null, 1],
            ['Custom', 'ပြင်ဆင်ခြင်း', 'custom', 0, 'fa fa-windows', null, 0],
            ['Add Custom', 'ထပ်ထည့်ခြင်း', 'addcustom', 0, 'fa fa-sitemap', null, 0],
            ['User Guide', 'အသုံးပြုနည်း', 'user-guide', 1, 'fa fa-book', null, 0],

            // children (parent resolved by link)
            ['Add Post', 'ပိုစ့်ထည့်ခြင်း', 'post/create', 2, 'fa fa-home', 'post', 1],
            ['Category', 'ကဏ္ဍတ', 'category', 2, 'fa fa-edit', 'post', 0],
            ['Block', 'ဘောက်လောက်တုန်း', 'block', 4, 'fa fa-table', 'post', 1],
            ['News and Events', 'သတင်းနှင့်ပွဲများ', 'news', 1, 'fa fa-newspaper-o', 'post', 1],
            ['Account', 'အကောင့်', 'account', 0, 'fa fa-desktop', 'settings', 1],
            ['Permission', 'ခွင့်ပြုချက်', 'permission', 0, 'fa fa-windows', 'settings', 1],
            ['Generals', 'အခြေခံ', 'general', 0, 'fa fa-bug', 'settings', 1],
            ['Configuration', 'ဖွဲ့စည်းမှု', 'configuration', 5, 'fa fa-cogs', 'settings', 1],
            ['Themes', 'ပုံစံများ', 'themes', 6, 'fa fa-paint-brush', 'settings', 1],
            ['Plugins', 'ပလပ်အင်များ', 'plugins', 7, 'fa fa-plug', 'settings', 1],
            ['Customer Report', 'ဖောက်သည်အစီရင်ခံစာ', 'reports/customer-report', 10, 'fa fa-users', 'reports', 1],
            ['Customer Report Export', 'ဖောက်သည် ထုတ်ယူ', 'reports/customer-report-export', 10, 'fa fa-download', 'reports', 0],
            ['Activity log', 'လုပ်ဆောင်ချက်မှတ်တမ်း', 'activity', 1, 'fa fa-history', 'reports', 1],
        ];

        foreach ($modules as [$name, $mm, $link, $weight, $icon, $parent, $section]) {
            Bp_module::insert([
                'module_name'    => $name,
                'module_name_mm' => $mm,
                'module_link'    => $link,
                'module_weight'  => $weight,
                'module_icon'    => $icon,
                'parent_id'      => $parent ? Bp_module::where('module_link', $parent)->value('module_id') : 0,
                'staff_id'       => 1,
                'section'        => $section,
                'created_at'     => '2016-06-3 00:36:29',
            ]);
        }
    }
}
