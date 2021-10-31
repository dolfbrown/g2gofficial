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
 namespace App\Http\Controllers\Installer\Helpers; use Exception; use Illuminate\Support\Facades\Artisan; use Symfony\Component\Console\Output\BufferedOutput; class FinalInstallManager { public function runFinal() { $outputLog = new BufferedOutput(); $this->generateKey($outputLog); $this->publishVendorAssets($outputLog); return $outputLog->fetch(); } private static function generateKey($outputLog) { try { if (!config("\x69\156\x73\x74\x61\154\154\x65\162\56\x66\x69\x6e\141\154\56\x6b\x65\x79")) { goto BKSFX; } Artisan::call("\x6b\x65\171\72\x67\x65\x6e\x65\162\x61\164\x65", ["\55\55\x66\x6f\x72\143\145" => true], $outputLog); BKSFX: } catch (Exception $e) { return static::response($e->getMessage(), $outputLog); } return $outputLog; } private static function publishVendorAssets($outputLog) { try { if (!config("\151\156\x73\164\x61\x6c\x6c\x65\162\x2e\x66\x69\156\x61\x6c\x2e\160\x75\x62\x6c\151\163\x68")) { goto lWwco; } Artisan::call("\x76\x65\x6e\x64\x6f\162\x3a\x70\x75\x62\x6c\151\163\x68", ["\55\x2d\x61\154\x6c" => true], $outputLog); lWwco: } catch (Exception $e) { return static::response($e->getMessage(), $outputLog); } return $outputLog; } private static function response($message, $outputLog) { return ["\x73\x74\x61\x74\x75\x73" => "\x65\162\162\x6f\x72", "\155\145\x73\163\x61\147\x65" => $message, "\144\142\x4f\165\164\x70\x75\x74\x4c\157\147" => $outputLog->fetch()]; } }
