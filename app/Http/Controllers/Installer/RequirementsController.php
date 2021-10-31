<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  2.0.1   |
    |              on 2021-06-06 11:53:16              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
/*
* Copyright (C) Incevio Systems, Inc - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
* Written by Munna Khan <help.zcart@gmail.com>, September 2018
*/
 namespace App\Http\Controllers\Installer; use Illuminate\Routing\Controller; use App\Http\Controllers\Installer\Helpers\RequirementsChecker; class RequirementsController extends Controller { protected $requirements; public function __construct(RequirementsChecker $checker) { $this->requirements = $checker; } public function requirements() { $phpSupportInfo = $this->requirements->checkPHPversion(config("\x69\x6e\x73\164\x61\x6c\154\145\162\x2e\143\x6f\x72\145\56\155\151\x6e\x50\150\160\x56\145\x72\163\151\x6f\156")); $requirements = $this->requirements->check(config("\151\156\163\164\x61\154\154\x65\x72\56\x72\x65\161\165\x69\x72\x65\155\145\156\x74\x73")); return view("\151\156\163\x74\141\x6c\154\x65\x72\x2e\x72\x65\x71\x75\151\x72\145\x6d\145\156\164\x73", compact("\x72\145\x71\165\151\162\145\155\145\156\x74\163", "\x70\x68\160\x53\x75\160\x70\x6f\x72\x74\x49\156\x66\x6f")); } }
