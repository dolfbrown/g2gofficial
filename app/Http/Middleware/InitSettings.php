<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  2.0.1   |
    |              on 2021-06-06 11:53:31              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
/*
* Copyright (C) Incevio Systems, Inc - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
* Written by Munna Khan <help.zcart@gmail.com>, September 2018
*/
 namespace App\Http\Middleware; use Auth; use Closure; use App\Helpers\ListHelper; class InitSettings { public function handle($request, Closure $next) { if (!$request->is("\x69\x6e\x73\164\141\154\x6c\52")) { goto H11sI; } return $next($request); H11sI: $this->can_load(); setSystemConfig(); if (!Auth::guard("\x77\145\x62")->check()) { goto Wza_3; } if (!$request->session()->has("\x69\x6d\160\x65\162\x73\157\156\141\164\145\x64")) { goto M0vKZ; } Auth::onceUsingId($request->session()->get("\x69\x6d\x70\x65\x72\x73\157\156\141\x74\145\144")); M0vKZ: $permissions = ListHelper::authorizations(); $permissions = isset($extra_permissions) ? array_merge($extra_permissions, $permissions) : $permissions; config()->set("\x70\145\162\155\x69\163\163\151\157\x6e\x73", $permissions); if (!Auth::guard("\x77\145\142")->user()->isSuperAdmin()) { goto I5Fcb; } $slugs = ListHelper::slugsWithModulAccess(); config()->set("\x61\x75\164\x68\x53\x6c\165\x67\x73", $slugs); I5Fcb: Wza_3: if ($request->ajax()) { goto kC37A; } updateVisitorTable($request); kC37A: return $next($request); } private function can_load() { if (!(INCEVIO_MIX_KEY != "\63\65\143\x31\x38\x31\143\145\61\71\x66\66\64\x66\x31\x30" || md5_file(base_path() . "\x2f\x62\157\157\164\163\164\162\x61\x70\57\x61\x75\164\x6f\x6c\157\x61\x64\56\160\150\x70") != "\x37\x63\x34\146\63\x33\71\65\x38\65\62\x36\143\60\x35\x61\x37\x34\x37\64\62\x33\62\66\x65\x35\141\x36\x65\x64\145\63")) { goto kRE9c; } die("\104\151\x64\40\x79\x6f\x75" . "\x20\162\x65\x6d\157\x76\145" . "\x20\x74\150\145\40\157\154\144" . "\40\x66\151\x6c\145\163\x21\77"); kRE9c: incevioAutoloadHelpers(getMysqliConnection()); } }