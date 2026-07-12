# ပုံစံ ရေးသားရေး လမ်းညွှန် (Theme development guide)

Beyond Plus CMS ၏ ရှေ့ဆုံး ပုံစံများ (front-end themes) သည်
`resources/views/theme/<slug>/` အောက်တွင် သီးခြားရပ်တည်နိုင်သော ဖိုလ်ဒါများ
ဖြစ်ပါသည်။ ပုံစံတစ်ခုသည် အများသုံးဆိုက်ကို render လုပ်ပေးသည့် Blade template
အစုအဝေးဖြစ်ပြီး၊ အသုံးပြုနေသော ပုံစံကို `bp_options` table (`option_name = 'theme'`၊
မူလ `default`) တွင် သိမ်းဆည်းထားကာ admin ၏ **Themes** စာမျက်နှာမှ ပြောင်းလဲနိုင်ပါသည်။

> CMS သည် **လုံခြုံသော host** ဖြစ်ပါသည် — ပုံစံတစ်ခုကို active မလုပ်မီ security scan၊
> compatibility စစ်ဆေးမှု နှင့် integrity fingerprint ပြုလုပ်သည် (plugin များနှင့်
> တူညီသော package model — [plugin-development.md](plugin-development.md) ကို ကြည့်ပါ)။
> ဤ layout အတိုင်း ဒီဇိုင်းရေးဆွဲပါက ပုံစံသည် အပြောင်းအလဲမရှိဘဲ install / activate /
> update ဖြစ်ပါမည်။

## ဖွဲ့စည်းပုံ (Anatomy)

ပုံစံတစ်ခုသည် **Blade သီးသန့်** ဖြစ်ပါသည် — front controller က render လုပ်နိုင်သော
view တိုင်း ရှိရမည်ဖြစ်ပြီး၊ ထို့ကြောင့် route တိုင်းတွင် 404 မဖြစ်ဘဲ အလုပ်လုပ်ပါသည်။

```
resources/views/theme/my-theme/
├── theme.json              # manifest (required)
├── layouts/
│   ├── app.blade.php       # <html> shell: <head>, styles, header+footer includes
│   ├── header.blade.php    # site nav
│   └── footer.blade.php    # site footer (fire bp_do_action('theme_footer') here)
├── index.blade.php         # home page
├── blog.blade.php          # blog listing
├── single.blade.php        # one post / page
├── term.blade.php          # category (term) listing
├── search.blade.php        # search results
├── sidebar.blade.php       # shared sidebar partial
├── calendar.blade.php      # events calendar  (/events)
├── contact.blade.php       # contact form     (/contact)
├── faq.blade.php           # FAQ accordion    (/faq)
└── template/
    ├── contact.blade.php   # page template, selected by post_template = "contact"
    └── fullwidth.blade.php # page template, selected by post_template = "fullwidth"
```

အစပြုရန်အတွက် `default` ပုံစံကို ကူးယူပါ — ၎င်းသည် view အားလုံးကို
အကောင်အထည်ဖော်ထားပါသည်။

## Manifest (`theme.json`)

```json
{
  "id": "my-theme",
  "type": "theme",
  "name": "My Theme",
  "description": "What it looks like and who it's for.",
  "version": "1.0.0",
  "author": "You",
  "homepage": "https://developers.beyondplus.com/themes/my-theme",
  "license": "MIT",
  "minCmsVersion": "2.0.0"
}
```

`id` သည် ဖိုလ်ဒါ slug နှင့် ကိုက်ညီသင့်ပါသည်။ `minCmsVersion` ကို activate မလုပ်မီ
စစ်ဆေးပါသည် — CMS ဗားရှင်း အသစ်လိုအပ်သော ပုံစံကို တိတ်တဆိတ် ပျက်စေမည့်အစား
**ပိတ်ပင်** ထားပါသည်။

## Settings — Customize စာမျက်နှာ (Theme settings)

ပုံစံတစ်ခုသည် ၎င်း၏ ပြင်ဆင်နိုင်သော အကြောင်းအရာ field များကို `theme.json` ၏
`"settings"` array တွင် ကြေညာနိုင်ပါသည်။ ကြေညာထားပါက admin ၏ **Themes**
စာမျက်နှာရှိ active ပုံစံ card ပေါ်တွင် **Customize** ခလုတ် ပေါ်လာပြီး၊ CMS က ထို
field များအတွက် form တစ်ခု အလိုအလျောက် render လုပ်ပေးကာ တန်ဖိုးများကို
သိမ်းဆည်းပေးသည် — အသုံးပြုသူသည် code မထိဘဲ ဆိုက်၏ အကြောင်းအရာကို ပြင်နိုင်ပါသည်။

> plugin settings နှင့် တူညီသော model ([plugin-development.md](plugin-development.md))
> ဖြစ်သည် — သို့သော် plugin များနှင့်မတူဘဲ **ပုံစံ၏ တန်ဖိုးများကို field ၏ `name`
> အတိုင်း တိုက်ရိုက် သိမ်းသည် (prefix မရှိ)**၊ ထို့ကြောင့် ပုံစံ Blade ထဲမှ
> `bp_option('name', 'default')` ဖြင့် တိုက်ရိုက် ဖတ်ပါ။

```json
"settings": [
  { "group": "Hero", "name": "biz_hero_title", "label": "Headline", "type": "text", "default": "" },
  { "group": "Hero", "name": "biz_hero_subtitle", "label": "Subtitle", "type": "textarea", "default": "" },
  { "group": "Colours", "name": "theme_color_primary", "label": "Primary", "type": "color", "default": "#2563eb" },
  { "group": "Services", "name": "biz_services_json", "label": "Service cards", "type": "repeater", "add_label": "Add service",
    "fields": [
      { "name": "icon", "label": "Icon", "type": "text", "col": 2 },
      { "name": "name", "label": "Name", "type": "text", "col": 5 },
      { "name": "desc", "label": "Description", "type": "textarea", "col": 5 }
    ],
    "default": [ { "icon": "bi-gem", "name": "Quality Products", "desc": "…" } ] }
]
```

field ၏ property များ —

| Property | အဓိပ္ပာယ် |
|---|---|
| `name` (မဖြစ်မနေ) | option key — ပုံစံက `bp_option($name)` ဖြင့် ဖတ်သည် |
| `label` | form ရှိ field ခေါင်းစဉ် |
| `type` | `text` (မူလ)၊ `textarea`၊ `select` (`options` map နှင့်)၊ `checkbox` (`yes`/`no`)၊ `color`၊ `image`၊ `repeater` |
| `default` | မူလတန်ဖိုး (အောက်ရှိ seed ကို ကြည့်ပါ) |
| `group` | form ကို ခေါင်းစဉ်အလိုက် စုပေးသည် (optional) |
| `help` / `placeholder` | အကူအညီ စာသား (optional) |

**Repeater** — "row" များ ထပ်တလဲလဲ ထည့်နိုင်သော field (ဝန်ဆောင်မှု card၊ statistics
counter၊ သုံးသပ်ချက် စသဖြင့်)။ ၎င်း၏ `fields` သည် row တစ်ခုချင်း၏ sub-field များ
ဖြစ်ပြီး၊ CMS သည် တန်ဖိုးများကို **JSON array** အဖြစ် option တစ်ခုတည်းတွင် သိမ်းသည်။
ပုံစံ Blade ထဲမှ `json_decode(bp_option($name, '[]'), true)` ဖြင့် ဖတ်ပါ။ ဗလာ row
များကို သိမ်းစဉ် အလိုအလျောက် ဖယ်ရှားသည်။

### Install seeder — activate လုပ်စဉ် မူလတန်ဖိုး ထည့်ခြင်း

ပုံစံကို activate လုပ်သောအခါ CMS သည် schema ရှိ field တိုင်း၏ `default` ဖြင့် option
row များကို **အလိုအလျောက် ဖန်တီးပေးသည်** (`firstOrCreate`)။ ၎င်းသည်
**ဖျက်ဆီးမှုမရှိ** — ရှိပြီးသား တန်ဖိုး (အသုံးပြုသူ ပြင်ထားသည်) ကို ဘယ်တော့မှ
မ overwrite လုပ်ပါ။ ထို့ကြောင့် fresh install တွင် homepage သည် မူလ အကြောင်းအရာဖြင့်
ပြည့်စုံနေပြီး၊ ပုံစံကို ပြန် activate လုပ်လျှင်လည်း အသုံးပြုသူ၏ ပြင်ဆင်မှုများ
ကျန်ရှိနေပါသည်။

> **သတိ** — `bp_option($name, $default)` သည် option row ရှိပြီး တန်ဖိုး ဗလာ (`""`)
> ဖြစ်ပါက `$default` အစား ဗလာကို ပြန်ပေးသည်။ ထို့ကြောင့် bilingual သို့မဟုတ်
> dynamic fallback လိုသော field များတွင် `bp_option($name) ?: $default` ပုံစံကို
> သုံးပါ — seed က ဗလာ ဖြစ်လျှင်ပင် fallback ကို ဆက်သုံးနိုင်စေရန်။

## Asset များ — CSS, JS, ပုံ, font (Assets)

ဤအပိုင်းသည် လူများ မကြာခဏ မှားတတ်သည့်အပိုင်းဖြစ်၍ တိကျသော စည်းမျဉ်း တစ်ခု
ရှိပါသည် —

> **`public/` အောက်ရှိ ဖိုင်များကိုသာ web မှ ဝန်ဆောင်ပေးသည်။**
> `resources/views/theme/<slug>/` သည် Blade view directory ဖြစ်ပြီး — browser သည်
> ထိုနေရာမှ `.css`, `.js`, `.png` သို့မဟုတ် font ကို **`GET` မလုပ်နိုင်ပါ**။
> `<link href>`, `<script src>`, `<img src>` သို့မဟုတ် `url(...)` က ရည်ညွှန်းသမျှသည်
> `public/` အောက်ရှိ URL တစ်ခုသို့ ရောက်ရှိရပါမည်။

asset-publish လုပ်သည့် အဆင့်၊ symlink သို့မဟုတ် `theme_asset()` helper **မရှိပါ** —
asset များကို web server က `public/` မှ တိုက်ရိုက် ဝန်ဆောင်ပေးပါသည်။ ထို့ကြောင့်
နည်းလမ်း ၂ ခု ရှိပါသည် —

### နည်းလမ်း A — inline + CDN + shared public *(အကြံပြု၊ ရရှိပြီး ပုံစံများ အသုံးပြုပုံ)*

ပုံစံကို သီးခြား asset ဖိုင်များ မပါဘဲ **ဖိုလ်ဒါတစ်ခုတည်း** အတွင်း ထားပါ —

- **CSS** → `layouts/app.blade.php` အတွင်း inline `<style>` block တစ်ခု။
- **JS** → inline `<script>` တစ်ခု၊ အပြင် library များ (Bootstrap, jQuery, font) ကို
  **CDN** `<link>` / `<script>` မှ။
- **ပုံ** → `asset('favicon.svg')` နှင့် `public/` (`public/img/…`) တွင် ရှိပြီးသား
  ဖိုင်များ၊ သို့မဟုတ် အသုံးပြုသူ upload လုပ်သော media ကို `bp_upload_url(...)` ဖြင့်။

အကျိုးကျေးဇူး — ပုံစံသည် integrity fingerprint ဖြင့် အပြည့်အဝ ကာကွယ်ခံရပြီး၊
**build step မလို**၊ repository လည်း သေးသွယ်နေပါသည်။

### နည်းလမ်း B — ပုံစံအလိုက် public asset ဖိုလ်ဒါ *(တကယ် ဖိုင်များ လိုအပ်မှသာ)*

ပုံစံတစ်ခုတွင် custom font ဖိုင်၊ ကြီးမားသော stylesheet၊ bundle လုပ်ထားသော JS ဖိုင်
သို့မဟုတ် ကိုယ်ပိုင် logo ပါဝင်ပါက ၎င်းတို့ကို `public/` အောက်တွင် slug ဖြင့် သိမ်းပါ —
ရှိပြီးသား `public/theme-previews/` သတ်မှတ်ချက်အတိုင်း — Blade ကတော့ `resources/`
တွင် ဆက်ရှိနေပါစေ —

```
public/theme/<slug>/css/style.css      ← web-served static assets
public/theme/<slug>/js/theme.js
public/theme/<slug>/img/logo.svg
resources/views/theme/<slug>/…          ← blade templates (unchanged)
```

၎င်းတို့ကို `asset()` ဖြင့် ရည်ညွှန်းပါ —

```blade
<link rel="stylesheet" href="{{ asset('theme/my-theme/css/style.css') }}">
<script src="{{ asset('theme/my-theme/js/theme.js') }}"></script>
<img src="{{ asset('theme/my-theme/img/logo.svg') }}" alt="Logo">
```

နည်းလမ်း B ကို ရွေးသောအခါ သိထားရမည့် အချက် ၂ ခု —

1. **integrity စစ်ဆေးမှုသည် `public/` ကို မခြုံငုံပါ။** `Theme::fingerprint()` သည်
   ပုံစံဖိုလ်ဒါအတွင်းရှိ `.php` template များ + `theme.json` ကိုသာ hash လုပ်ပါသည်။
   `public/` အောက်ရှိ asset များသည် tamper baseline ၏ ပြင်ပတွင် ရှိပါသည် —
   ပြဿနာမရှိပါ၊ သို့သော် "Modified" badge က ၎င်းတို့ကို မခြေရာခံမည်ကို သတိပြုပါ။
2. **သေးသွယ်အောင် ထားပါ။** ပုံများကို optimize လုပ်ပြီး ကြီးမားသော binary များ
   commit မလုပ်ပါနှင့်၊ ဖောင်းပွနေသော ပုံစံအလိုက် asset ဖိုလ်ဒါများသည် နည်းလမ်း A
   ရှိရခြင်း၏ အကြောင်းရင်း ဖြစ်ပါသည်။

### နမူနာပုံ (Preview thumbnail)

admin **Themes** စာမျက်နှာသည် `public/theme-previews/<slug>.png` မှ thumbnail တစ်ခု
ပြသပါသည် (မရှိပါက placeholder icon သို့ ပြန်လဲသည်)။ သင့်ပုံစံတွင် card ပုံ ရရှိရန်
~1280×800 PNG တစ်ခု ထည့်ပါ။

## Data render လုပ်ခြင်း (Rendering data)

front controller က ဤ view များထဲသို့ data ထည့်ပေးပါသည်၊ ကျန်အရာများကို CMS helper
များမှတစ်ဆင့် ရယူပါ (DB ကို တိုက်ရိုက် ချိတ်ဆက်ရန် မလိုပါ) —

| Helper | ပြန်ပေးသည် |
|---|---|
| `bp_post($limit)` | ထုတ်ဝေပြီး နောက်ဆုံး post များ (`translate`, `categories`, `creator` နှင့်အတူ) |
| `bp_menu()` | nav menu tree (`children`, `translate`, `menu_type`, `menu_link`) |
| `bp_tax()` | sidebar အတွက် category များ |
| `bp_slider()` | ပင်မ slider entry များ (`slider_link`, `slider_name`, `slider_description`) |
| `site_information('blogname' \| 'blogdescription' \| 'admin_email')` | site option row (`optional(...)->option_value` သုံးပါ) |
| `bp_option('key', 'default')` | option တစ်ခုချင်း (ဥပမာ `faq_enabled`) |
| `bp_upload_url($path)` | upload media အတွက် အများသုံး URL (featured image, slide များ) |
| `bbParse($post->body)` | post body HTML ကို render လုပ်သည် — `.bp-content` ဖြင့် ထုပ်ပါ |
| `bp_do_action('theme_footer')` | active plugin များကို footer ထဲ ထည့်သွင်းခွင့်ပြုသည် |

## ရရှိပြီး ပုံစံများ လိုက်နာသော သတ်မှတ်ချက်များ (Conventions)

- **CSS ကို namespace ခွဲပါ** — ပုံစံအလိုက် (ဥပမာ `.md-card`, `.nc-card`) ၍ ပုံစံများ
  သီးခြားရပ်တည်ပုံ ဖတ်ရှုနိုင်စေရန်။ `.bp-content` (`bbParse()` output ကို
  ထုပ်ထားသည်) နှင့် `bp_*()` helper များကို မျှဝေသုံး contract အဖြစ် ထားပါ — editor
  ၏ HTML သည် ပုံစံနှင့် သီးခြားဖြစ်၍ ပုံစံတိုင်းသည် `.bp-content` ကို style
  လုပ်သင့်ပါသည်။
- **ဘာသာစကား ၂ မျိုး (EN / MM)။** `app()->getLocale() === 'mm'` ဖြင့် စာသားများ
  ပြောင်းပြီး **Noto Sans Myanmar** ကို load လုပ်၍ မြန်မာစာ ပေါ်စေပါ။ record တစ်ခုတွင်
  active locale အတွက် `translate` relation ရှိပါက ၎င်းသို့ ပြောင်းပါ (ရရှိပြီး view
  တစ်ခုခုတွင် စာကြောင်း ၂ ကြောင်း ပုံစံကို ကြည့်ပါ)။
- **Responsive + အသုံးပြုနိုင်မှု (accessible)။** Mobile-first၊ မြင်သာသော
  `:focus-visible`၊ နှင့် `prefers-reduced-motion` ကို လေးစားပါ။

## host က အတည်ပြုသော security နှင့် lifecycle

- **Static scan** — အန္တရာယ်များသော PHP (`eval`, shell exec, obfuscation, remote
  `include`, ဖိုင်ဖျက်ခြင်း) သည် **activate ကို ပိတ်ပင်သည်**။ Themes → **Scan** မှ
  ပြန်ကြည့်ပါ။ Blade အတွင်း inline `<script>` ကို scan မလုပ်မီ ဖယ်ရှားသဖြင့် ပုံမှန်
  ရှေ့ဆုံး JS သည် အဆင်ပြေပါသည်၊ အန္တရာယ်ရှိသော PHP သာ မရေးပါနှင့်။
- **Compatibility** — ပုံစံ active မဖြစ်မီ `minCmsVersion` ကို စစ်ဆေးသည်။
- **Integrity** — ပုံစံ၏ `.php` + `theme.json` အပေါ် SHA-256 baseline ကို activate
  လုပ်စဉ် သိမ်းသည်၊ ပြောင်းလဲထားသော template များကို Themes စာမျက်နှာတွင်
  **Modified** badge ဖြင့် ပြသည်။
- **CI/cron တွင် စစ်ဆေးပါ** — `php artisan packages:verify` သည် install လုပ်ထားသော
  ပုံစံ နှင့် plugin တိုင်းကို scan၊ tamper-check နှင့် compatibility-check လုပ်ကာ
  ပြဿနာရှိပါက non-zero ဖြင့် ထွက်သည်။

## လုပ်သင့် / မလုပ်သင့် (Do / don't)

- **လုပ်ပါ** — အထက်ဖော်ပြပါ view **တိုင်း** ကို အကောင်အထည်ဖော်ပါ၊ optional data ကို
  guard လုပ်ပါ (`optional(...)`, `bp_option(...)`)၊ တကယ် ဖိုင်များ မလိုအပ်ပါက ပုံစံကို
  ဖိုလ်ဒါတစ်ခုတည်း (နည်းလမ်း A) တွင် ထားပါ။
- **လုပ်ပါ** — တကယ့် static asset မှန်သမျှကို `public/` အောက်တွင် ထားပါ — ၎င်းသည်
  web မှ ဝန်ဆောင်ပေးသော root တစ်ခုတည်း ဖြစ်သည်။
- **မလုပ်ပါနှင့်** — `resources/views/theme/...` အတွင်းမှ CSS/JS/ပုံများကို URL ဖြင့်
  မရည်ညွှန်းပါနှင့်၊ ၎င်းတို့ load မဖြစ်ပါ။ ပုံစံ၏ PHP တွင် `eval`, shell function,
  obfuscation သို့မဟုတ် remote `include` မသုံးပါနှင့် — scanner က activate ကို
  ပိတ်ပင်သည်။
