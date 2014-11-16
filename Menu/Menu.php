<?php

namespace YMD\Menu;

use \User;
use \Auth;
use \Module;
use \Log;

class Menu {

    private $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }//__construct

    public function getMenu()
    {
        //$data = $this->user->with('roles','roles.modules')->find(Auth::user()->id);
        /*
        $data = Module::distinct()->select('module.name','module.title','module.js_controller')
            ->join('role_module','role_module.module_id','=', 'module.id')
            ->join('role','role.id','=','role_module.role_id')
            ->join('user_role','user_role.role_id','=', 'role.id')
            ->where('user_role.user_id','=',Auth::user()->id)
            ->where('module.visible','=',true)
            ->OrderBy('module.view_order')
            ->get();
        */
        $data = $this->user->with('roles')
                ->with(array('roles.modules'=>function($query){
                            $query->where('visible','=',true);
                            $query->orderBy('view_order','ASC');
                        } ))
                ->where('id',Auth::user()->id)
                ->get();
        print_r($data->toArray());
        dd($data->toArray());
        return $this->prepare($data);

    }//getMenu

    private function prepare($data)
    {
        $ret = array();
        foreach($data as $k=>$v)
        {
            $t = explode("/",$v->title);
            $command = '$ret["'.join('"]["', $t).'"] = serialize(array("name"=>$v->name,"type"=>$v->js_controller)) ;';
            eval($command);
        }//foreach
        //print_r($ret);
        $m = '<ul class="nav navbar-nav navbar-right">'.$this->getItem($ret).'</ul>';

        //print_r($m);
        return $m;
    }//prepare

    private function getItem($m, $k='')
    {
        if(! is_array($m))
        {
            $m = unserialize($m);
            Log::info("MENU: ", $m);


            //$m[4] = ($m[4]) ? $m[4] : "";
            return '<li><a class="nc" ng-click="mc({name:\''.$m['name'].'\'})">'.$k.'</a></li>';
            //return '<li><a onclick="menuclick({name:\''.$m[0].'\',url:\''.route($m[0]).'\',type:\''.$m[1].'\',title:\''.$m[2].'\',mid:\''.$m[3].'\',divid:\''.str_replace('.','-',$m[0]).'\'});return false;">'.$k.'</a></li>';
        }
        $t = array();
        foreach($m as $k=>$v)
        {
            $r = self::getItem($v,$k);
            $y = '<a  class="nc" class="dropdown-toggle" data-toggle="dropdown">'.$k.' <span class="caret"></span></a><ul class="dropdown-menu" role="menu">';
            if(is_array($v))
                $t[] = '<li class="dropdown">'.$y.$r.'</ul></li>';
            else
                $t[] = $r;
        }
        return implode(PHP_EOL,$t);

    }//getItem
}//Menu