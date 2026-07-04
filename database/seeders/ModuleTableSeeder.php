<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Bp_module;

class ModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bp_module::truncate();
        $module = array(
                    array("Dashboard","ပင်မစာမျက်နှာ","/",0,"fa fa-dashboard",0,1),
                    array("Post","ပိုစ့်","post",1,"fa fa-edit",0,1),
                    array("Page","စာမျက်နှာ","page",2,"fa fa-edit",0,1),
                    array("Menu","အညွန်းများ","menu",3,"fa fa-table",0,1),
                    array("Media","မီဒီယာ","media",4,"fa fa-desktop",0,1),
                    array("Slider","ကြော်ငြာ","slider",5,"fa fa-image",0,1),
                    array("FAQ","အမေးအဖြေများ","faq",6,"fa fa-question-circle",0,1),
                    array("Feedback","တုံ့ပြန်ချက်များ","feedback",7,"fa fa-comments",0,1),
                    array("User Management","အဖွဲ့ဝင်များ","user",8,"fa fa-windows",0,1),
                    array("Settings","ထိန်းချုပ်ရေး","settings",9,"fa fa-bug",0,1),
                    array("Custom","ပြင်ဆင်ခြင်း","custom",0,"fa fa-windows",0,0),
                    array("Add Custom","ထပ်ထည့်ခြင်း","addcustom",0,"fa fa-sitemap",0,0));
        $this->createModule($module);
        $child = array(
                    array("Add Post","ပိုစ့်ထည့်ခြင်း","post/create",2,"fa fa-home",2,0),
                    array("Category","ကဏ္ဍတ","category",2,"fa fa-edit",2,0),
                    array("Block","ဘောက်လောက်တုန်း","block",2,"fa fa-table",2,0),
                    array("Account","အကောင့်","account",0,"fa fa-desktop",8,0),
                    array("Permission","ခွင့်ပြုချက်","permission",0,"fa fa-windows",8,0),
                    array("Generals","အခြေခံ","general",0,"fa fa-bug",8,1),
                    array("Configuration","ဖွဲ့စည်းမှု","configuration",5,"fa fa-cogs",8,1),
                    array("Themes","ပုံစံများ","themes",6,"fa fa-paint-brush",8,1),
                    array("Plugins","ပလပ်အင်များ","plugins",7,"fa fa-plug",8,1)
                );
        $this->createModule($child);
        
    }

    public function createModule($module){
        for ($i=0; $i < count($module); $i++) {
            $Bp_module = [
                'module_name'       => $module[$i][0],
                'module_name_mm'    => $module[$i][1],
                'module_link'       => $module[$i][2],
                'module_weight'     => $module[$i][3],
                'module_icon'       => $module[$i][4],
                'parent_id'         => $module[$i][5],
                'staff_id'          => '1',
                'section'           => $module[$i][6],
                'created_at'        => '2016-06-3 00:36:29'
            ];
            Bp_module::insert($Bp_module);
        }
    }
}