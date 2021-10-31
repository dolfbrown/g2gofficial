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
 namespace App\Http\Controllers\Installer\Helpers; use Exception; use Illuminate\Http\Request; class EnvironmentManager { private $envPath; private $envExamplePath; public function __construct() { $this->envPath = base_path("\56\145\156\x76"); $this->envExamplePath = base_path("\x2e\x65\x6e\166\x2e\x65\x78\x61\155\x70\154\x65"); } public function getEnvContent() { if (file_exists($this->envPath)) { goto rfqUX; } if (file_exists($this->envExamplePath)) { goto Kq0Za; } touch($this->envPath); goto eMEB6; Kq0Za: copy($this->envExamplePath, $this->envPath); eMEB6: rfqUX: return file_get_contents($this->envPath); } public function getEnvPath() { return $this->envPath; } public function getEnvExamplePath() { return $this->envExamplePath; } public function saveFileClassic(Request $input) { $message = trans("\151\x6e\x73\x74\141\x6c\x6c\145\x72\137\155\145\163\x73\141\147\145\x73\x2e\145\156\x76\x69\162\x6f\156\155\145\x6e\164\56\163\x75\143\143\x65\163\163"); try { file_put_contents($this->envPath, $input->get("\x65\156\166\103\157\156\x66\151\147")); } catch (Exception $e) { $message = trans("\x69\x6e\x73\x74\x61\154\x6c\145\x72\x5f\x6d\x65\x73\x73\141\x67\145\163\56\x65\x6e\166\151\162\157\x6e\x6d\145\156\x74\x2e\145\162\162\x6f\162\x73"); } return $message; } }
