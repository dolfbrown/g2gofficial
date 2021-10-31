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
 namespace App\Http\Controllers\Installer; use Exception; use Illuminate\Support\Facades\DB; use Illuminate\Routing\Controller; use App\Http\Controllers\Installer\Helpers\DatabaseManager; class DatabaseController extends Controller { private $databaseManager; public function __construct(DatabaseManager $databaseManager) { $this->databaseManager = $databaseManager; } public function database() { if ($this->checkDatabaseConnection()) { goto A8mzZ; } return redirect()->back()->withErrors(["\144\x61\164\141\x62\x61\x73\x65\137\143\157\x6e\x6e\145\x63\164\151\157\x6e" => trans("\x69\x6e\163\x74\x61\x6c\x6c\x65\162\137\155\x65\163\x73\141\x67\145\163\56\x65\x6e\x76\151\162\157\156\155\x65\x6e\x74\56\x77\x69\172\x61\x72\x64\x2e\146\157\162\155\56\x64\142\x5f\x63\157\156\x6e\145\143\164\151\x6f\x6e\137\x66\141\x69\154\x65\x64")]); A8mzZ: ini_set("\x6d\141\170\x5f\145\170\145\143\165\164\151\x6f\156\x5f\x74\x69\x6d\145", 600); $response = $this->databaseManager->migrateAndSeed(); return redirect()->route("\x49\156\x73\164\141\x6c\x6c\x65\162\x2e\x66\151\156\141\x6c")->with(["\155\x65\x73\x73\x61\x67\145" => $response]); } private function checkDatabaseConnection() { try { DB::connection()->getPdo(); return true; } catch (Exception $e) { return false; } } }
