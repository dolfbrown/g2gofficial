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
 namespace App\Http\Controllers\Installer; use Illuminate\Routing\Controller; use App\Http\Controllers\Installer\Helpers\InstalledFileManager; use App\Http\Controllers\Installer\Helpers\DatabaseManager; class UpdateController extends Controller { use \App\Http\Controllers\Installer\Helpers\MigrationsHelper; public function welcome() { return view("\151\x6e\163\164\141\154\x6c\145\162\x2e\x75\160\x64\x61\x74\x65\x2e\x77\x65\154\143\157\155\145"); } public function overview() { $migrations = $this->getMigrations(); $dbMigrations = $this->getExecutedMigrations(); return view("\x69\x6e\x73\164\141\x6c\x6c\x65\162\56\x75\x70\x64\x61\x74\x65\x2e\x6f\x76\145\162\166\x69\x65\167", ["\156\165\x6d\x62\145\162\117\146\x55\x70\x64\x61\x74\x65\163\x50\145\x6e\x64\x69\156\x67" => count($migrations) - count($dbMigrations)]); } public function database() { $databaseManager = new DatabaseManager(); $response = $databaseManager->migrateAndSeed(); return redirect()->route("\114\x61\x72\141\x76\145\154\125\x70\144\x61\x74\x65\x72\72\72\146\151\156\141\154")->with(["\155\x65\163\163\141\147\x65" => $response]); } public function finish(InstalledFileManager $fileManager) { $fileManager->update(); return view("\151\x6e\x73\x74\141\x6c\x6c\145\162\x2e\165\160\x64\x61\164\145\56\146\x69\156\151\x73\150\145\x64"); } }
