<?php

Mock::generate('ToppaAutoLoaderWp');
Mock::generate('ToppaFunctionsFacadeWp');

class UnitPost2Post extends UnitTestCase {
    private $post2post;

    public function __construct() {
        $this->UnitTestCase();
    }

    public function setUp() {
        $autoLoader = new MockToppaAutoLoaderWp();
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $post = new stdClass;
        $post->post_title = 'Hello world!';
        $functionsFacade->setReturnValue('getPost', $post);
        $this->post2post = new Post2Post($autoLoader, $functionsFacade);
    }

    public function testSetShortcodeWithInvalidArgument() {
        try {
            $this->post2post->setShortcode('string');
        }

        catch (Exception $e) {
            $this->pass('Received expected exception');
        }
    }

    public function testSetShortcodeWithValidArgument() {
        $shortcode = array(
            'type' => 'slug',
            'value' => 'hello-world',
            'text' => '  my favorite post', // extra spaces in front should get trimmed
            'attributes' => 'onclick="foo()"',
            'anchor' => 'named_anchor'
        );

        $trimmedShortcode = $shortcode;
        $trimmedShortcode['text'] = 'my favorite post';
        $this->assertEqual($this->post2post->setShortcode($shortcode), $trimmedShortcode);
    }

    public function testSetLinkTextFromShortcodeIfProvidedWithNoText() {
        $this->post2post->setShortcode(array('text' => ''));
        $this->assertNull($this->post2post->setLinkTextFromShortcodeIfProvided());

    }

    public function testSetLinkTextFromShortcodeIfProvidedWithText() {
        $this->post2post->setShortcode(array('text' => 'foo'));
        $this->assertEqual($this->post2post->setLinkTextFromShortcodeIfProvided(), 'foo');
    }

    public function testSetPostIdWithInvalidType() {
        $this->post2post->setShortcode(array('type' => 'slug'));
        $this->assertNull($this->post2post->setPostId());
    }

    public function testSetPostIdWithInvalidValue() {
        try {
            $this->post2post->setShortcode(array('type' => 'id', 'value' => 'foo'));
            $this->post2post->setPostId();
        }

        catch (Exception $e) {
            $this->pass('Received expected exception');
        }
    }

    public function testSetPostIdWithValidShortcode() {
        $this->post2post->setShortcode(array('type' => 'id', 'value' => 2));
        $this->assertEqual($this->post2post->setPostId(), 2);
    }

    public function testSetLinkTextByPostIdIfNeededWithValidShortcode() {
        $this->post2post->setShortcode(array('type' => 'id', 'value' => 2));
        $this->assertEqual($this->post2post->setLinkTextByPostIdIfNeeded(), 'Hello world!');
    }

    public function testSetLinkTextByPostIdIfNeededWithInvalidShortcode() {
        $autoLoader = new ToppaAutoLoaderWp();
        $functionsFacade = new ToppaFunctionsFacadeWp();
        $this->post2post = new Post2Post($autoLoader, $functionsFacade);

        $this->post2post->setShortcode(array('type' => 'id', 'value' => 2));
        var_dump($this->post2post->setLinkTextByPostIdIfNeeded());
    }
}