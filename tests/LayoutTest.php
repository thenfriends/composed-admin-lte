<?php

namespace ThenFriends\ComposedAdminLte\Tests;

use ThenLabs\ComposedViews\AbstractView;
use ThenFriends\ComposedAdminLte\Layout;
use ThenFriends\ComposedAdminLte\Tests\TestCase;
use Wa72\HtmlPageDom\HtmlPageCrawler;
use Wa72\HtmlPageDom\HtmlPage;
use Closure;

setTestCaseNamespace(__NAMESPACE__);
setTestCaseClass(TestCase::class);

define('DATA_NAMES', [
    'title', 'skin', 'layoutType', 'contentTitle', 'contentDescription',
    'logo', 'logoLink', 'leftFooterContent', 'rightFooterContent'
]);

define('SIDEBAR_NAMES', [
    'mainSidebar', 'content'
]);

define('SKIN_VALUES', [
    'blue', 'black', 'purple',
    'yellow', 'red', 'green'
]);

define('LAYOUT_TYPE_VALUES', [
    'fixed',
    'layout-boxed',
    'layout-top-nav',
    'sidebar-collapse',
    'sidebar-mini',
]);

testCase('LayoutTest.php', function () {
    testCase('a layout is created', function () {
        setUp(function () {
            $this->layout = new Layout;
            $this->layoutModel = $this->layout->getModel();
        });

        createMacro('the view of the layout', function (Closure $tests) {
            testCase('the view of the layout', function () use ($tests) {
                setUp(function () {
                    $this->layoutView = new HtmlPageCrawler($this->layout->render());
                });

                $tests();
            });
        });

        createMacro('the body element', function (Closure $tests) {
            testCase('the body element', function () use ($tests) {
                setUp(function () {
                    $this->body = $this->layoutView->filter('body');
                });

                $tests();
            });
        });

        foreach (DATA_NAMES as $dataName) {
            test("has the '{$dataName}' data", function () use ($dataName) {
                $this->assertViewHasData($dataName, $this->layout);
            });
        }

        foreach (SIDEBAR_NAMES as $sidebarName) {
            test("has the '{$sidebarName}' sidebar", function () use ($sidebarName) {
                $this->assertViewHasSidebar($sidebarName, $this->layout);
            });
        }

        test('the skin is blue by default', function () {
            $this->assertEquals('blue', $this->layout->getSkin());
        });

        test('testing the domain values for the skin data', function () {
            $this->assertEquals(SKIN_VALUES, $this->layoutModel['data']['skin']['values']);
        });

        test('the layoutType is "sidebar-mini" by default', function () {
            $this->assertEquals('sidebar-mini', $this->layout->getLayoutType());
        });

        test('testing the domain values for the layoutType data', function () {
            $this->assertEquals(LAYOUT_TYPE_VALUES, $this->layoutModel['data']['layoutType']['values']);
        });

        useMacro('the view of the layout', function () {
            test('has the expected styles', function () {
                $expected = [
                    'bower_components/bootstrap/dist/css/bootstrap.min.css',
                    'bower_components/font-awesome/css/font-awesome.min.css',
                    'bower_components/Ionicons/css/ionicons.min.css',
                    'thenfriends/composed-admin-lte/css/AdminLTE.min.css',
                    'thenfriends/composed-admin-lte/css/skins/skin-blue.min.css',
                ];

                foreach ($expected as $uri) {
                    $this->assertCount(1, $this->layoutView->filter("link[href$=\"{$uri}\"]"));
                }
            });

            test('has the expected scripts', function () {
                $expected = [
                    'bower_components/jquery/dist/jquery.min.js',
                    'bower_components/bootstrap/dist/js/bootstrap.min.js',
                    'thenfriends/composed-admin-lte/js/adminlte.min.js',
                ];

                foreach ($expected as $uri) {
                    $this->assertCount(1, $this->layoutView->filter("script[src$=\"{$uri}\"]"));
                }
            });

            test('the link of the logo is equal to "javascript:;"', function () {
                $this->assertEquals(
                    'javascript:;',
                    $this->layoutView->filter('a.logo')->getAttribute('href')
                );
            });

            test('the main sidebar is empty', function () {
                $this->assertEmpty(trim($this->layoutView->filter('.sidebar-main')->getInnerHtml()));
            });

            test('the content sidebar is empty', function () {
                $this->assertEmpty(trim($this->layoutView->filter('.sidebar-content')->getInnerHtml()));
            });

            useMacro('the body element', function () {
                test('has the "skin-blue" css class', function () {
                    $this->assertTrue($this->body->hasClass('skin-blue'));
                });

                test('has the "sidebar-mini" css class', function () {
                    $this->assertTrue($this->body->hasClass('sidebar-mini'));
                });
            });
        });

        testCase('sets a new title to the layout', function () {
            setUp(function () {
                $this->title = uniqid();
                $this->layout->setTitle($this->title);
            });

            useMacro('the view of the layout', function () {
                test('has the expected title', function () {
                    $page = new HtmlPage($this->layoutView->saveHTML());
                    $this->assertEquals($this->title, $page->getTitle());
                });
            });
        });

        testCase('sets a new skin value to the layout', function () {
            setUp(function () {
                $this->value = 'green';
                $this->layout->setSkin($this->value);
            });

            useMacro('the view of the layout', function () {
                useMacro('the body element', function () {
                    test('has the expected skin css class', function () {
                        $this->assertFalse($this->body->hasClass('skin-blue'));
                        $this->assertTrue($this->body->hasClass('skin-'.$this->value));
                    });
                });
            });
        });

        testCase('sets a new layoutType value to the layout', function () {
            setUp(function () {
                $this->value = 'fixed';
                $this->layout->setLayoutType($this->value);
            });

            useMacro('the view of the layout', function () {
                useMacro('the body element', function () {
                    test('has the expected layout type css class', function () {
                        $this->assertFalse($this->body->hasClass('sidebar-mini'));
                        $this->assertTrue($this->body->hasClass($this->value));
                    });
                });
            });
        });

        testCase('sets a new contentTitle value to the layout', function () {
            setUp(function () {
                $this->value = uniqid();
                $this->layout->setContentTitle($this->value);
            });

            useMacro('the view of the layout', function () {
                test('has the expected content title', function () {
                    $this->assertContains(
                        $this->value,
                        $this->layoutView->filter('section.content-header > h1')->html()
                    );
                });
            });
        });

        testCase('sets a new contentDescription value to the layout', function () {
            setUp(function () {
                $this->value = uniqid();
                $this->layout->setContentDescription($this->value);
            });

            useMacro('the view of the layout', function () {
                test('has the expected content description', function () {
                    $this->assertContains(
                        $this->value,
                        $this->layoutView->filter('section.content-header small')->html()
                    );
                });
            });
        });

        testCase('sets a new logo link to the layout', function () {
            setUp(function () {
                $this->value = uniqid();
                $this->layout->setLogoLink($this->value);
            });

            useMacro('the view of the layout', function () {
                test('has the expected logo link', function () {
                    $this->assertContains(
                        $this->value,
                        $this->layoutView->filter('a.logo')->getAttribute('href')
                    );
                });
            });
        });

        testCase('sets a new logo to the layout', function () {
            setUp(function () {
                $this->value = uniqid();
                $this->layout->setLogo($this->value);
            });

            useMacro('the view of the layout', function () {
                test('has the expected logo', function () {
                    $this->assertContains(
                        $this->value,
                        $this->layoutView->filter('a.logo')->getInnerHtml()
                    );
                });
            });
        });

        testCase('sets a new left footer content to the layout', function () {
            setUp(function () {
                $this->value = uniqid();
                $this->layout->setLeftFooterContent($this->value);
            });

            useMacro('the view of the layout', function () {
                test('has the expected left footer content', function () {
                    $this->assertContains(
                        $this->value,
                        $this->layoutView->filter('.left-footer-content')->getInnerHtml()
                    );
                });
            });
        });

        testCase('sets a new right footer content to the layout', function () {
            setUp(function () {
                $this->value = uniqid();
                $this->layout->setRightFooterContent($this->value);
            });

            useMacro('the view of the layout', function () {
                test('has the expected right footer content', function () {
                    $this->assertContains(
                        $this->value,
                        $this->layoutView->filter('.right-footer-content')->getInnerHtml()
                    );
                });
            });
        });

        testCase('adds a view to the main sidebar of the layout', function () {
            setUp(function () {
                $view = new class extends AbstractView {
                    public function getView(array $data = []): string
                    {
                        return 'the content of the view';
                    }
                };

                $this->layout->mainSidebar->addChild($view);
            });

            useMacro('the view of the layout', function () {
                test('the main sidebar contains the content of the his childs', function () {
                    $this->assertContains(
                        'the content of the view',
                        $this->layoutView->filter('.sidebar-main')->getInnerHtml()
                    );
                });
            });
        });

        testCase('adds a view to the content sidebar of the layout', function () {
            setUp(function () {
                $view = new class extends AbstractView {
                    public function getView(array $data = []): string
                    {
                        return 'the content of the view';
                    }
                };

                $this->layout->content->addChild($view);
            });

            useMacro('the view of the layout', function () {
                test('the content sidebar contains the content of the his childs', function () {
                    $this->assertContains(
                        'the content of the view',
                        $this->layoutView->filter('.sidebar-content')->getInnerHtml()
                    );
                });
            });
        });
    });
});
