<?php
declare(strict_types=1);

namespace ThenFriends\ComposedAdminLte;

use ThenLabs\ComposedViews\AbstractCompositeView;
use ThenLabs\ComposedViews\TextView;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Submenu extends AbstractCompositeView
{
    protected $text;
    protected $icon;
    protected $active;
    protected $open;

    public function __construct(string $text, ?string $icon, bool $active = false, bool $open = false)
    {
        parent::__construct();

        $this->text = $text;
        $this->icon = $icon;
        $this->active = $active;
        $this->open = $open;
    }

    public function addItem(string $text, string $link, bool $active = false): self
    {
        $li_class = $active ? 'class="active"' : '';

        $item = new TextView('
            <li '.$li_class.'><a href="'.$link.'">'.$text.'</a></li>
        ');

        $this->addChild($item);

        return $this;
    }

    public function end(): Menu
    {
        return $this->parent;
    }

    public function getView(array $data = []): string
    {
        ob_start();
        require 'templates/submenu.php';
        return ob_get_clean();
    }
}
