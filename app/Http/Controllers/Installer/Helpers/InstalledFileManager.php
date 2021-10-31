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
 namespace App\Http\Controllers\Installer\Helpers; class InstalledFileManager { public function create() { $installedLogFile = storage_path("\151\x6e\x73\x74\141\x6c\154\145\144"); $dateStamp = date("\x59\x2f\x6d\57\144\40\x68\x3a\151\72\163\141"); if (!file_exists($installedLogFile)) { goto Jhn73; } $message = trans("\151\x6e\163\164\141\154\154\145\x72\x5f\x6d\x65\x73\163\x61\x67\145\x73\x2e\x75\160\x64\141\164\145\x72\56\154\x6f\x67\56\x73\165\143\143\145\x73\163\x5f\x6d\145\163\x73\x61\147\145") . $dateStamp; file_put_contents($installedLogFile, $message . PHP_EOL, FILE_APPEND | LOCK_EX); goto jQmAZ; Jhn73: $message = trans("\151\x6e\x73\164\141\154\x6c\x65\162\137\155\145\163\163\141\147\x65\163\x2e\151\156\x73\x74\141\154\154\x65\x64\x2e\163\x75\x63\x63\x65\x73\x73\137\x6c\x6f\x67\x5f\x6d\145\163\163\x61\147\145") . $dateStamp . "\xa"; file_put_contents($installedLogFile, $message); jQmAZ: return $message; } public function update() { return $this->create(); } }
