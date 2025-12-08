<?php

namespace Vasil\Turshija;

use Vasil\Turshija\Helpers\App;
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
        $posts = glob(App::root() . '/web/posts/*.md');

        foreach ($posts as $p) {
            $data = Parse::content($p);

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

            File::save(App::root() . '/dist' . $data->getUrl(), $final);
        }
    }

    private function prepareAssets()
    {
        $src = App::root() . '/templates/default/assets';
        $dest = App::root() . '/dist/assets';

        // @TODO make this without using shell
        shell_exec("cp -r $src $dest");
    }

    private function loadIndex()
    {
        return Parse::content(App::root() . '/web/index.md');
    }
}
