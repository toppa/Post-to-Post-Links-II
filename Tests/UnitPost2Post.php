<?php

Mock::generate('ToppaAutoLoaderWp');
Mock::generate('ToppaFunctionsFacadeWp');
Mock::generate('ToppaDatabaseFacadeWp');

class UnitPost2Post extends UnitTestCase {
    private $post2post;

    public function __construct() {
        $this->UnitTestCase();
    }

    private function basicSetUp() {
        $this->finalizeSetUp(new MockToppaFunctionsFacadeWp(), new MockToppaDatabaseFacadeWp());
    }

    private function finalizeSetUp($functionsFacade, $dbFacade) {
        $autoLoader = new MockToppaAutoLoaderWp();
        $this->post2post = new Post2Post($autoLoader, $functionsFacade, $dbFacade);
    }

    public function testSetShortcodeWithInvalidArgumentType() {
        try {
            $this->basicSetUp();
            $this->post2post->setShortcode('string');
        }

        catch (Exception $e) {
            $this->pass('Received expected exception');
        }
    }

    public function testSetShortcodeWithoutRequiredAttributes() {
        try {
            $this->basicSetUp();
            $this->post2post->setShortcode(array('foo' => 'bar'));
        }

        catch (Exception $e) {
            $this->pass('Received expected exception');
        }
    }

    public function testSetShortcodeWithValidType() {
        $slug = 'hello-world';
        $this->basicSetUp();
        $shortcode = array('slug' => $slug);
        $this->post2post->setShortcode($shortcode);
        $this->assertEqual($this->post2post->setShortcode($shortcode), $shortcode);
    }

    public function testSetShortcodeWithDeprecatedType() {
        $this->basicSetUp();
        $shortcode = array('type' => 'slug', 'value' => 'hello-world');
        $this->post2post->setShortcode($shortcode);
        $expected = $shortcode;
        $expected['slug'] = 'hello-world';
        $this->assertEqual($this->post2post->setShortcode($shortcode), $expected);
    }

    public function testSetShortcodeWithAllAttributes() {
        $this->basicSetUp();
        $shortcode = array(
            'slug' => 'hello-world',
            'text' => '  my favorite post', // extra spaces in front should get trimmed
            'attributes' => 'onclick="foo()"',
            'anchor' => 'named_anchor'
        );
        $trimmedShortcode = $shortcode;
        $trimmedShortcode['text'] = 'my favorite post';
        $this->assertEqual($this->post2post->setShortcode($shortcode), $trimmedShortcode);
    }

    public function testSetTitleAndLinkUrlFromPostSlugWithEmptySlug() {
        try {
            $this->basicSetUp();
            $this->post2post->setShortcode(array('slug' => ''));
            $this->post2post->setTitleAndLinkUrlFromPostSlug();
        }

        catch (Exception $e) {
            $this->pass('Received expected exception');
        }
    }

    public function testSetTitleAndLinkUrlFromPostSlugWithPostNotFound() {
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $functionsFacade->setReturnValue('escHtml', 'hello-world');
        $dbFacade = new MockToppaDatabaseFacadeWp();
        $dbFacade->setReturnValue('selectSqlRow', null);
        $this->finalizeSetUp($functionsFacade, $dbFacade);

        try {
            $this->post2post->setShortcode(array('slug' => 'hello-world'));
            $this->post2post->setTitleAndLinkUrlFromPostSlug();
        }

        catch (Exception $e) {
            $this->pass('Received expected exception');
        }
    }

    private function successfulPostQuerySetUp() {
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $functionsFacade->setReturnValue('getPermalink', 'http://localhost/wordpress/hello-world');
        $functionsFacade->setReturnValue('escHtml', 'my link text');
        $dbFacade = new MockToppaDatabaseFacadeWp();
        $dbFacade->setReturnValue('sqlSelectRow', array('ID' => 1234, 'post_title' => 'Hello World!'));
        $this->finalizeSetUp($functionsFacade, $dbFacade);
    }

    public function testSetTitleAndLinkUrlFromPostSlugWithPostFound() {
        $this->successfulPostQuerySetUp();
        $this->post2post->setShortcode(array('slug' => 'hello-world'));
        $this->post2post->setTitleAndLinkUrlFromPostSlug();
        $this->assertEqual($this->post2post->getLinkUrl(), 'http://localhost/wordpress/hello-world');
        $this->assertEqual($this->post2post->getTitle(), 'Hello World!');
    }

    public function testSetTitleAndLinkUrlFromPostIdWithNonNumericPostId() {
        try {
            $this->basicSetUp();
            $this->post2post->setShortcode(array('id' => 'foo'));
            $this->post2post->setTitleAndLinkUrlFromPostId();
        }

        catch (Exception $e) {
            $this->pass('Received expected exception');
        }
    }

    public function testSetTitleAndLinkUrlFromPostIdWithPostNotFound() {
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $functionsFacade->setReturnValue('getPost', null);
        $functionsFacade->setReturnValue('escHtml', '1234');
        $this->finalizeSetUp($functionsFacade, new MockToppaDatabaseFacadeWp());

        try {
            $this->post2post->setShortcode(array('id' => '1234'));
            $this->post2post->setTitleAndLinkUrlFromPostId();
        }

        catch (Exception $e) {
            $this->pass('Received expected exception');
        }
    }

    public function testSetTitleAndLinkUrlFromPostIdWithPostFound() {
        $functionsFacade = new MockToppaFunctionsFacadeWp();
        $post = array('post_title' => 'Hello world!');
        $functionsFacade->setReturnValue('getPost', $post);
        $functionsFacade->setReturnValue('getPermalink', 'http://localhost/hello-world');
        $this->finalizeSetUp($functionsFacade, new MockToppaDatabaseFacadeWp());

        $this->post2post->setShortcode(array('id' => '1234'));
        $this->post2post->setTitleAndLinkUrlFromPostId();
        $this->assertEqual($this->post2post->getLinkUrl(), 'http://localhost/hello-world');
        $this->assertEqual($this->post2post->getTitle(), 'Hello world!');
    }

    public function testSetLinkTextWithTextFromShortcode() {
        $this->assertEqual($this->post2post->setLinkText('my link text'), 'my link text');
    }

    public function testSetLinkTextWithTextFromShortcodeAttribute() {
        $this->successfulPostQuerySetUp();
        $this->post2post->setShortcode(array('slug' => 'hello-world', 'text' => 'my link text'));
        $this->assertEqual($this->post2post->setLinkText(), 'my link text');
    }

    public function testSetLinkTextWithTextFromPostTitle() {
        $this->successfulPostQuerySetUp();
        $this->post2post->setShortcode(array('slug' => 'hello-world'));
        $this->post2post->setTitleAndLinkUrlFromPostSlug();
        $this->assertEqual($this->post2post->setLinkText(), 'Hello World!');
    }

    public function testSetAnchor() {
        $this->post2post->setShortcode(array('slug' => 'hello-world', 'anchor' => 'more'));
        $this->post2post->setLinkAnchor();
        $this->assertEqual($this->post2post->setLinkAnchor(), '#more');
    }
    
    public function testSetP2pLink() {
        $this->successfulPostQuerySetUp();
        $this->post2post->setShortcode(array(
            'slug' => 'hello-world',
            'anchor' => 'more',
            'text' => 'my link text',
            'attributes' => "id='my-id'")
        );
        $this->post2post->setTitleAndLinkUrlFromPostSlug();
        $this->post2post->setLinkAnchor();
        $this->post2post->setLinkText();
        $expected = "<a href='http://localhost/wordpress/hello-world#more' title='Hello World!' id='my-id'>my link text</a>";
        $this->assertEqual($this->post2post->setP2pLink(), $expected);
    }
}