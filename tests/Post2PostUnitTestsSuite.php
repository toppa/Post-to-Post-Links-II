<?php

// this is needed for simpletest's addFile method
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

$p2pPath = dirname(dirname(__FILE__));
$p2pParentDir = basename($p2pPath);
$p2pAutoLoaderPath = $p2pPath . '/lib/P2pAutoLoader.php';

if (file_exists($p2pAutoLoaderPath)) {
    require_once($p2pAutoLoaderPath);
    new P2pAutoLoader('/' . $p2pParentDir . '/lib');
}

class P2pUnitTestsSuite extends TestSuite {
    function __construct() {
        parent::__construct();
        $this->addFile('UnitPost2Post.php');
    }
}