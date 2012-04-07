<?php

// this is needed for simpletest's addFile method
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));

$p2pTestsAutoLoaderPath = dirname(__FILE__) . '/../../toppa-plugin-libraries-for-wordpress/ToppaAutoLoaderWp.php';

if (file_exists($p2pTestsAutoLoaderPath)) {
    require_once($p2pTestsAutoLoaderPath);
    $p2pTestsToppaAutoLoader = new ToppaAutoLoaderWp('/toppa-plugin-libraries-for-wordpress');
    $p2pTestsAutoLoader = new ToppaAutoLoaderWp('/post-to-post-links-ii');
}

class ButtonableUnitTestsSuite extends TestSuite {
    function __construct() {
        parent::__construct();
        $this->addFile('UnitPost2Post.php');
    }
}