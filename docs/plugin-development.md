# ပလပ်အင် ရေးသားရေး လမ်းညွှန် (Plugin development guide)

Beyond Plus CMS ၏ plugin များသည် `/plugins` အောက်ရှိ သီးခြားရပ်တည်နိုင်သော
ဖိုလ်ဒါများ ဖြစ်ပါသည်။ plugin တစ်ခုသည် hook များ၊ database schema၊ admin/ရှေ့ဆုံး
စာမျက်နှာများ နှင့် asset များ ပါဝင်နိုင်ပြီး၊ CMS သည် ၎င်း၏ lifecycle တစ်ခုလုံး
(install → activate → update → deactivate → uninstall) ကို security၊ compatibility
နှင့် recovery တို့ ပါဝင်လျက် စီမံပေးပါသည်။

> CMS သည် **လုံခြုံသော host** ဖြစ်ပါသည်။ နောင်တွင် တရားဝင် portal
> (`developers.beyondplus.com`) သည် ဖြန့်ဝေရေး လမ်းကြောင်း ဖြစ်လာမည် —
> [plugin-portal.md](plugin-portal.md) ကို ကြည့်ပါ။ သင့် plugin ကို ဤ manifest
> အတိုင်း ဒီဇိုင်းရေးဆွဲပါက ထိုနေရာတွင် အပြောင်းအလဲမရှိဘဲ publish ဖြစ်ပါမည်။

## ဖွဲ့စည်းပုံ (Anatomy)

```
plugins/my-plugin/
├── plugin.json          # manifest (required)
├── my-plugin.php        # main file — registers hooks (required)
├── migrations/          # plugin-owned schema (optional)
│   └── 2026_01_01_000001_create_my_table.php
├── routes.php           # front-end / admin routes (optional)
├── views/               # Blade views, namespaced my-plugin::name (optional)
├── assets/              # css/js/images (optional)
├── lang/                # translations (optional)
└── uninstall.php        # cleanup on uninstall (optional)
```

## Manifest (`plugin.json`)

```json
{
  "id": "my-plugin",
  "type": "plugin",
  "category": "Communication",
  "name": "My Plugin",
  "description": "What it does.",
  "description_mm": "ဘာလုပ်ပေးသည်ကို မြန်မာဘာသာဖြင့် (optional)။",
  "version": "1.0.0",
  "author": "You",
  "homepage": "https://developers.beyondplus.com/plugins/my-plugin",
  "license": "MIT",
  "minCmsVersion": "2.0.0",
  "requires": { "php": "8.1", "extensions": ["curl"] },
  "permissions": ["http", "database"],
  "main": "my-plugin.php",
  "admin_menu": { "title": "My Plugin", "link": "my-plugin", "icon": "fa fa-cog", "parent": 8 }
}
```

| Field | ရည်ရွယ်ချက် |
|---|---|
| `id` / `type` / `name` / `version` | တည်ငြိမ်သော package identity (portal နှင့် မျှဝေသုံး) |
| `minCmsVersion`, `requires` | Compatibility — မပြည့်မီပါက activation ကို **ပိတ်ပင်** သည် |
| `permissions` | plugin လုပ်ဆောင်ချက်ကို ကြေညာသည် (ယခုအခါ သတင်းအချက်အလက်သာ) |
| `main` | boot ချိန်တွင် hook များ register လုပ်ရန် load လုပ်သော ဖိုင် |
| `admin_menu` | admin စာမျက်နှာသို့ sidebar link + access ခွင့်ပြုချက် ထည့်သည် |
| `description_mm` | *(optional)* မြန်မာ locale (`mm`) အတွက် ဖော်ပြချက်။ Plugins စာမျက်နှာသည် locale သည် `mm` ဖြစ်ပြီး ဤ field ရှိမှသာ ၎င်းကို ပြသကာ၊ မဟုတ်ပါက `description` (အင်္ဂလိပ်) သို့ ပြန်ကျသည် |

> **ဘာသာစကား ၂ မျိုး (i18n)** — `description_mm` ကို ကြေညာရုံဖြင့် Plugins
> စာမျက်နှာတွင် plugin ဖော်ပြချက်ကို မြန်မာလို ပြသပါမည် (code ပြင်ရန် မလို)။
> `name` ကဲ့သို့ ကျန် identity field များကို ဘာသာမပြန်ဘဲ ထားပါ။

## Settings (ပလပ်အင် configuration စာမျက်နှာ)

plugin သည် ၎င်း၏ config field များကို `plugin.json` တွင် ကြေညာသည် — CMS က ၎င်းအတွက်
settings form တစ်ခု render လုပ်ပေးပြီး (Plugins → card ပေါ်ရှိ **Settings**) တန်ဖိုးများ
သိမ်းဆည်းပေးသည်။ core Configuration စာမျက်နှာတွင် ဘာမှ ထပ်ထည့်ရန် မလိုပါ။

```json
"settings": [
  { "name": "api_url",   "label": "API URL",   "type": "text",     "default": "https://api.example.com", "help": "Base endpoint." },
  { "name": "api_token", "label": "API Token", "type": "password" },
  { "name": "mode",      "label": "Mode",      "type": "select",   "options": { "live": "Live", "test": "Test" } }
]
```

Field `type` — `text` (မူလ)၊ `password`၊ `textarea`၊ `select` (`options` နှင့်)၊
`checkbox` (`yes`/`no`)။ သိမ်းဆည်းထားသော တန်ဖိုးများကို သင့် plugin code မှ ဤသို့
ဖတ်ပါ —

```php
$token = bp_plugin_option('my-plugin', 'api_token');   // stored as plugin.my-plugin.api_token
```

`"test"` block တစ်ခု ထည့်ပါက settings စာမျက်နှာတွင် "Send test" ခလုတ် ပေါ်လာပြီး၊
ရိုက်ထည့်ထားသော လက်ခံသူဖြင့် သတ်မှတ် hook ကို run ပေးသည် —

```json
"test": { "hook": "send_sms", "label": "Send a test SMS to", "placeholder": "09xxxxxxxxx" }
```

## Hooks

သင့် main ဖိုင်မှ —

```php
// Action — side effect
bp_add_action('theme_footer', fn () => print '<p>Hi</p>');

// Filter — transform a value (must return it)
bp_add_filter('the_content', fn ($html) => $html.'<hr>');
```

core/theme မှ ကိုယ်ပိုင် hook ကို ဤသို့ trigger လုပ်ပါ — `bp_do_action('name', ...$args)`
/ `bp_apply_filters('name', $value, ...$args)`။ priority (နိမ့်သည် အရင်) ကို ရွေးချယ်
ထည့်နိုင်သည်။

Provider ဥပမာ — delivery channel တစ်ခု အကောင်အထည်ဖော်ခြင်း —

```php
bp_add_filter('send_sms', function ($sent, $to, $message) {
    if ($sent) return $sent;                 // already handled
    // ...call your gateway...
    return true;                              // delivered
});
```

## Database

Laravel migration များကို `migrations/` တွင် ထည့်ပါ။ ၎င်းတို့သည် **activate** တွင်
run ပြီး၊ apply ပြီးသားဆိုပါက ကျော်သွားသည် (ထို့ကြောင့် update များတွင် အသစ်များသာ
run သည်)၊ **deactivate** တွင် ဆက်ထားပြီး၊ **uninstall** တွင် rollback ပြန်လုပ်သည်။
အပို ရှင်းလင်းမှုအတွက် `uninstall.php` ထည့်ပါ။

## စာမျက်နှာများ (Pages — routes + views)

`routes.php` (active ဖြစ်နေစဉ်သာ load လုပ်သည်) — ကိုယ်ပိုင် middleware ကြေညာပါ —

```php
Route::middleware('admins')->prefix('bp-admin')->group(function () {
    Route::get('my-plugin', fn () => view('my-plugin::index'));
});
```

View များသည် `views/` တွင် ရှိပြီး `view('my-plugin::index')` အဖြစ် render လုပ်သည်။
`/bp-admin/*` စာမျက်နှာတစ်ခုကို ရောက်ရှိနိုင်စေရန် နှင့် sidebar တွင် ပြသရန် manifest
သို့ `admin_menu` ထည့်ပါ (CMS သည် activate တွင် module + access ကို register လုပ်ပြီး
deactivate တွင် ဖယ်ရှားသည်)။

## host က အတည်ပြုသော security နှင့် lifecycle

- **Static scan** — အန္တရာယ်များသော code (`eval`, shell exec, obfuscation, remote
  include) သည် **activation ကို ပိတ်ပင်သည်**။ Plugins → Scan မှ ပြန်ကြည့်ပါ။
- **Compatibility** — activation မတိုင်မီ `minCmsVersion` / `requires` ကို စစ်ဆေးသည်။
- **Integrity** — activate တွင် SHA-256 baseline သိမ်းသည်၊ ပြောင်းလဲထားသော ဖိုင်များကို
  Plugins စာမျက်နှာတွင် "Modified" ဖြင့် အမှတ်အသားပြုသည်။
- **Recovery** — load ချိန်တွင် error ဖြစ်သော plugin ကို အလိုအလျောက် disable လုပ်ပြီး
  အစီရင်ခံသည်၊ ထို့ကြောင့် ဆိုက်ကို ပျက်မသွားစေနိုင်ပါ။ သင့် main ဖိုင်ကို side-effect
  နည်းအောင်၊ ကာကွယ်မှုရှိအောင် ထားပါ။
- **Permissions** — Plugins module access ရှိသော admin များသာ plugin များ စီမံနိုင်သည်၊
  လုပ်ဆောင်ချက်တိုင်းကို audit-log မှတ်တမ်းတင်သည်။

## package များ စစ်ဆေးခြင်း (CI / cron)

install လုပ်ထားသော plugin နှင့် theme တိုင်းအပေါ် security sweep — static scan၊
integrity (tamper) check နှင့် compatibility check — ကို command တစ်ခုတည်းဖြင့်
run ပါ —

```bash
php artisan packages:verify          # table report
php artisan packages:verify --json   # machine-readable
php artisan packages:verify --strict # also fail on warnings
```

package တစ်ခုခုတွင် critical scan finding ရှိလျှင်၊ activate ပြီးနောက် ပြောင်းလဲထားလျှင်၊
သို့မဟုတ် incompatible ဖြစ်လျှင် **non-zero ဖြင့် ထွက်သည်** — ထို့ကြောင့် tamper ဖြစ်ထား
သို့မဟုတ် အန္တရာယ်ရှိသော package ကို စောစီးစွာ ဖမ်းမိရန် CI သို့မဟုတ် cron job တွင်
ချိတ်ဆက်ထားနိုင်ပါသည်။

## လုပ်သင့် / မလုပ်သင့် (Do / don't)

- **လုပ်ပါ** — မရှိသေးသော table/config များအတွက် guard လုပ်ပါ (`Schema::hasTable`,
  `bp_option(...)`)၊ တိတ်တဆိတ် fail ဖြစ်စေပြီး boot ကို မြန်အောင် ထားပါ။
- **မလုပ်ပါနှင့်** — `eval`, shell function, obfuscation သို့မဟုတ် remote `include`
  မသုံးပါနှင့် — scanner က ၎င်းတို့ကို ပိတ်ပင်ပြီး portal ကလည်း ငြင်းပယ်ပါမည်။
