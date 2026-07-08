<?php

namespace App;

use DateTime;
use Smarty\Smarty;

class View
{
    private Smarty $smarty;

    public function __construct()
    {
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir(dirname(__DIR__) . '/templates');
        $this->smarty->setCompileDir(dirname(__DIR__) . '/templates_c');
        $this->smarty->registerPlugin('modifier', 'nice_date', [$this, 'niceDate']);
    }

    public function render(string $template, array $data = []): string
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        return $this->smarty->fetch($template);
    }

    public function niceDate(string $value): string
    {
        return (new DateTime($value))->format('F d, Y');
    }
}
