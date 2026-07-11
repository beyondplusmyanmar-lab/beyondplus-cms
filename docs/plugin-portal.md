# Plugin & theme portal — ဖွဲ့စည်းတည်ဆောက်ပုံ နှင့် roadmap

CMS ကို ယခုအချိန်တွင် ကောင်းမွန်၍ လုံခြုံသော **package host** တစ်ခုအဖြစ်
ဒီဇိုင်းရေးဆွဲထားပြီး၊ နောင်တွင် ပေါ်ပေါက်လာမည့် **တရားဝင် portal** မှ package များကို
ဖွဲ့စည်းတည်ဆောက်ပုံ မပြောင်းလဲဘဲ အသုံးပြုနိုင်ရန် ရည်ရွယ်ထားပါသည်။ portal သည်
ecosystem ဝန်ဆောင်မှုတစ်ခုဖြစ်ပြီး၊ installation တိုင်း၏ အစိတ်အပိုင်း မဟုတ်ပါ။

```
Beyond Plus CMS
    → Plugin Manager  (install · scan · verify · migrate · activate · audit)
    → Official Portal (future distribution channel)
```

## အဆင့် ၁ — local package များ (ရရှိပြီး)

CMS သည် local `/plugins` directory မှ plugin များကို internet မလိုအပ်ဘဲ ပြည့်စုံ၍
လုံခြုံသော lifecycle ဖြင့် host လုပ်ပေးသည် —

- discover / activate / deactivate / uninstall
- static **security scan** (အန္တရာယ်များသော code ကို ပိတ်ပင်သည်)
- **compatibility** စစ်ဆေးမှုများ (`minCmsVersion`, PHP, extensions)
- plugin ပိုင် **migrations** (activate တွင် run၊ uninstall တွင် rollback)
- **integrity** baseline (SHA-256) + tamper ရှာဖွေတွေ့ရှိမှု
- **recovery mode** (crash ဖြစ်သော plugin ကို အလိုအလျောက် disable လုပ်၍ ဆိုက်
  ဆက်လက် အလုပ်လုပ်နေသည်)
- **permission** gating + **audit** log

Theme များကိုလည်း **တူညီသောနည်း** ဖြင့် host လုပ်သည် (အကောင်အထည်ဖော်ပြီး) —
တစ်ခုစီသည် `resources/views/theme/<slug>` တွင် `theme.json` manifest နှင့်အတူ ရှိပြီး၊
မျှဝေသုံး `App\Support\PackageGuard` က ၎င်းတို့ကို တူညီသော **security scan**
(Blade-aware — inline `<script>` နှင့် comment များကို လျစ်လျူရှုသည်)၊
**compatibility** စစ်ဆေးမှု နှင့် **integrity** fingerprint ပေးသည်။ scan မအောင်မြင်သော
theme ကို ဘယ်တော့မှ active မလုပ်ပါ။

## အဆင့် ၂ — တရားဝင် portal (နောင်တွင်၊ သီးခြား ဝန်ဆောင်မှု)

`developers.beyondplus.com` သည် အခြေခံအဆောက်အအုံ အားလုံးကို မျှဝေသုံးသော
**package အမျိုးအစား ၂ မျိုး** (`plugin` နှင့် `theme`) အတွက် ecosystem တစ်ခုတည်း
ပေးမည်ဖြစ်သည် —

```
Official Portal
├── Catalog (plugins + themes)
├── Developer accounts / Publisher dashboard
├── Docs + SDK
├── Release management + version history
├── Reviews / changelogs / security advisories
├── Signing (every release signed)
└── API (consumed by the CMS)
```

### Sign ထားသော release များ — ယုံကြည်မှု အဆင့်မြှင့်တင်ခြင်း

Heuristic scanning သည် အန္တရာယ် ကာကွယ်မှုဖြစ်ပြီး၊ စစ်မှန်ကြောင်း သက်သေ မဟုတ်ပါ။
portal က release တိုင်းကို sign လုပ်ပေးသဖြင့် CMS သည် မူရင်းအရင်းအမြစ်ကို စိစစ်နိုင်သည် —

```
Developer → upload → portal scans → portal SIGNS release
CMS → download → verify signature → verify checksum → install
```

release bundle တစ်ခုတွင် signature တစ်ခု ပါဝင်ပြီး၊ install မလုပ်မီ CMS က portal ၏
public key ဖြင့် စစ်ဆေးသည် —

```
my-plugin-1.2.0.zip
├── plugin.json
├── signature.json      # signature over the package hash
└── ...
```

### CMS ဘက်ခြမ်း ချိတ်ဆက်မှု (သေးငယ်၍ ဖြည့်စွက်သာ)

package များတွင် **တည်ငြိမ်သော metadata** (`id`, `type`, `version`, `minCmsVersion`,
`requires`, …) ရှိပြီးဖြစ်၍၊ portal API သည် CMS ဖတ်ပြီးသား ပုံသဏ္ဌာန်အတိုင်း
ဝန်ဆောင်ပေးနိုင်သည်။ portal ထည့်ခြင်းသည် internal အသစ်များ မဟုတ်ဘဲ *source* အသစ်
တစ်ခုသာ ထည့်သွင်းသည် —

- `plugin_registry_url` option → "တရားဝင် plugin/theme များ ရှာဖွေရန်"
- `GET {registry}/api/packages?type=plugin` → catalog
- download → **signature စစ်ဆေး** → `/plugins` သို့ ဖြေချ → ရှိပြီးသား activate
  lifecycle ကို run
- version နှိုင်းယှဉ်မှုဖြင့် "update available"; **လုံခြုံသော update များ** (verify →
  temp သို့ extract → migrate → atomically swap → health-check → commit၊ မဟုတ်ပါက
  rollback)

## ရည်ရွယ်ချက်ရှိရှိ မတည်ဆောက်ရသေးသည်များ

Marketplace UI၊ remote install/update နှင့် signature စိစစ်မှုတို့သည် portal
ပေါ်ပေါက်လာသည်အထိ စောင့်ဆိုင်းနေသည်။ CMS သည် **အတည်ပြု စိုးမိုးရေး** တာဝန်များ
(scan, verify, lifecycle, migrations, audit, recovery) ကို ဆက်လက် ကိုင်ဆောင်ထားပြီး၊
portal သည် **ဖြန့်ဝေရေး လမ်းကြောင်း** ဖြစ်သည်။

## ရိုးသားသော ကန့်သတ်ချက် (The honest boundary)

PHP plugin များသည် CMS process အတွင်း run သဖြင့် ၎င်း၏ အခွင့်အရေးများကို ရရှိသည် —
process/container သီးခြားခွဲခြားခြင်း မရှိဘဲ အပြည့်အဝ sandbox လုပ်၍ မရပါ။ signature၊
scanning၊ integrity နှင့် review တို့သည် အန္တရာယ်ကို လျှော့ချသည်၊ ပြည့်စုံသော security
ကန့်သတ်ချက်တော့ မဟုတ်ပါ။ ထို့ကြောင့် လမ်းညွှန်ချက်မှာ — **ယုံကြည်ရသော
အရင်းအမြစ်များမှသာ install လုပ်ပါ၊ sign ထားသော release များကို ဦးစားပေးပါ၊ `/plugins`
ကို web user အတွက် read-only ထားပါ၊ update များကို ပြန်လည် သုံးသပ်ပါ။**
