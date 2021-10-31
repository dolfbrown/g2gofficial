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
 namespace App\Http\Controllers\Installer; use Illuminate\Routing\Controller; use Illuminate\Http\Request; use Illuminate\Routing\Redirector; use App\Http\Controllers\Installer\Helpers\EnvironmentManager; use Validator; class EnvironmentController extends Controller { protected $EnvironmentManager; public function __construct(EnvironmentManager $environmentManager) { $this->EnvironmentManager = $environmentManager; } public function environmentMenu() { return view("\x69\x6e\163\x74\x61\154\x6c\145\162\x2e\x65\156\x76\x69\162\x6f\156\155\x65\156\164"); } public function environmentWizard() { } public function environmentClassic() { $envConfig = $this->EnvironmentManager->getEnvContent(); return view("\151\x6e\x73\x74\x61\154\x6c\145\162\x2e\145\156\x76\151\162\x6f\x6e\x6d\145\156\x74\x2d\x63\154\x61\163\x73\x69\143", compact("\x65\156\166\103\157\x6e\x66\x69\x67")); } public function saveClassic(Request $input, Redirector $redirect) { $message = $this->EnvironmentManager->saveFileClassic($input); return $redirect->route("\111\x6e\163\x74\141\x6c\154\145\x72\x2e\x65\156\x76\x69\x72\157\156\155\145\156\164\x43\154\x61\x73\163\151\143")->with(["\x6d\145\163\x73\141\147\x65" => $message]); } }
