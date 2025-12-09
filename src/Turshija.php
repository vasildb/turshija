<?php

namespace Vasil\Turshija;

use Vasil\Turshija\Helpers\App;
use Vasil\Turshija\Helpers\Parse;
use Vasil\Turshija\Helpers\Template;
use Vasil\Turshija\Helpers\File;

class Turshija
{
    private $websiteData = null;

    public function __construct($from, $to)
    {
        if (!is_dir($from))
            throw new \Exception($from . ' does not exist.');

        if (!is_dir($to))
            mkdir($to, 0755);

        if ((count(scandir($to)) != 2))
            throw new \Exception($to . ' is not empty.');
    }

    public function go(): int
    {
        $this->websiteData = $this->loadIndex();

        $posts = $this->preparePosts();
        $this->prepareHomepage($posts);
        $this->prepareAssets();

        return 0;
    }

    private function preparePosts()
    {
        $posts = glob(App::contentsDir() . '/posts/*.md');
        $postData = [];

        foreach ($posts as $p) {
            $data = Parse::content($p);
            $postData[] = $data;

            $header = Template::render('header.php');
            $footer = Template::render('footer.php');
            $post = Template::render('post.php', ['post' => $data->getHtml()]);

            $final = Template::render('index.php', [
                'header' => $header,
                'footer' => $footer,
                'content' => $post,
                'title' => $data->getProp('title') . ' - ' . $this->websiteData->getProp('title'),
                'website' => $this->websiteData,
            ]);

            File::save(App::exportDir() . $data->getUrl(), $final);
        }

        return $postData;
    }

    private function prepareHomepage(array $posts)
    {
        $header = Template::render('header.php');
        $footer = Template::render('footer.php');
        $homepage = Template::render('homepage.php', ['posts' => $posts]);

        $final = Template::render('index.php', [
            'header' => $header,
            'footer' => $footer,
            'content' => $homepage,
            'title' => $this->websiteData->getProp('title'),
            'website' => $this->websiteData,
        ]);
        File::save(App::exportDir() . '/index.html', $final);
    }

    private function prepareAssets()
    {
        $src = App::root() . '/templates/default/assets/';
        $dest = App::exportDir();

        // @TODO make this without using shell
        shell_exec("cp -r $src $dest");
    }

    private function loadIndex()
    {
        return Parse::content(App::contentsDir() . '/index.md');
    }
}
