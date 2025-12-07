<?php

namespace Vasil\Turshija\Helpers;

class Template
{
    public static function render($view, $data = []): string
    {
        $templatePath = '../templates/default/' . $view;

        if (!is_array($data)) {
            throw new \Exception('Invalid template variables.');
        }

        foreach ($data as $k => $v) {
            ${$k} = $v;
        }

        ob_start();
        include($templatePath);
        $templateContents = ob_get_contents();
        ob_end_clean();
        return $templateContents;
    }
}
