<?php

namespace Vasil\Turshija;

use Vasil\Turshija\Helpers\Parse;
use Vasil\Turshija\Helpers\Template;
use Vasil\Turshija\Helpers\File;

class Turshija
{
    private $websiteData = null;

    public function go(): int
    {
        $this->websiteData = $this->loadIndex();

        $this->preparePosts();
        $this->prepareAssets();

        return 0;
    }

    private function preparePosts()
    {
        $posts = glob('../web/posts/*.md');

        foreach ($posts as $p) {
            $contents = file_get_contents($p);
            $name = substr(basename($p), 0, -3) . '.html';
            $data = Parse::content($contents);

            $header = Template::render('header.php');
            $footer = Template::render('footer.php');
            $content = Template::render('post.php', ['post' => $data->getHtml()]);

            $final = Template::render('index.php', [
                'header' => $header,
                'footer' => $footer,
                'content' => $content,
                'title' => $data->getProp('title') . ' - ' . $this->websiteData->getProp('title'),
                'website' => $this->websiteData,
            ]);

            File::save('../dist/' . $name, $final);
        }
    }

    private function prepareAssets()
    {
        $src = '../templates/default/assets';
        $dest = '../dist/assets';

        // @TODO make this without using shell
        shell_exec("cp -r $src $dest");
    }

    private function loadIndex()
    {
        $contents = file_get_contents('../web/index.md');
        return Parse::content($contents);
    }
}
