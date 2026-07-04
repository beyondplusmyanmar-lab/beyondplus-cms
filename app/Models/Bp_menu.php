<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Bp_menu extends Model
{
    use LogsActivity;

    protected $primaryKey = 'menu_id';
    protected $table = 'bp_menus';

    protected $fillable = [
         'menu_name','menu_link','post_id','menu_weight','menu_icon','parent_id' ,'menu_type','lang', 'translate_id','staff_id','created_at','updated_at'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('menu')
            ->logOnly(['menu_name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $event) => trim(sprintf(
                '%s the menu item “%s”', $event, \Illuminate\Support\Str::limit((string) $this->menu_name, 60)
            )));
    }

    public function setMenulinkAttribute($value){
        $this->attributes['menu_link'] = str_replace(' ', '-', strtolower($value));
    }

    public function Parent(){
        return $this->belongsTo('App\Models\Bp_menu', 'parent_id','menu_id');
    }

    public function Children()
    {
        return $this->hasMany('App\Models\Bp_menu','parent_id','menu_id')->orderBy('menu_weight','asc');
    }

    public function Childrenfindid() 
    {
        return $this->hasMany('App\Models\Bp_menu','parent_id','menu_id')->orderBy('menu_weight','desc');
    }

    public function Post()
    {
        return $this->belongsTo('App\Models\Bp_post','post_id','id');
    }

    public function translate()
    {
        return $this->belongsTo('App\Models\Bp_menu','menu_id','translate_id');
    }
}
