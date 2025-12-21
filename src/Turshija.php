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
        $this->prepareSitemap($posts);

        return 0;
    }

    private function preparePosts()
    {
        $posts = glob(App::contentsDir() . '/posts/*.md');
        $postData = [];

        foreach ($posts as $p) {
            $data = Parse::content($p);
            $postData[] = $data;

            $header = Template::render('header.php', [
                'title' => $this->websiteData->getProp('title')
            ]);
            $footer = Template::render('footer.php');
            $post = Template::render('post.php', [
                'post' => $data->getHtml(),
                'title' => $data->getProp('title')
            ]);

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
        $header = Template::render('header.php', [
            'title' => $this->websiteData->getProp('title')
        ]);
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
        $src2 = App::contentsDir();
        $dest = App::exportDir();

        // @TODO make this without using shell
        shell_exec("cp -r $src $dest");
        shell_exec("cp -r $src2/assets/* $dest/assets/");
    }

    private function loadIndex()
    {
        return Parse::content(App::contentsDir() . '/index.md');
    }

    private function prepareSitemap($posts)
    {
        $xml = new \XMLWriter();
        $xml->openURI(App::exportDir() . '/sitemap.xml');
        $xml->startDocument('1.0', 'UTF-8');

        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        $xml->startElement('url');
        $xml->writeElement('loc', $this->websiteData->getProp('base-url'));
        $xml->writeElement('lastmod', date('Y-m-d'));
        $xml->endElement();

        foreach ($posts as $p) {
            $xml->startElement('url');
            $xml->writeElement('loc', trim($this->websiteData->getProp('base-url'), '/') . $p->getUrl());
            $xml->writeElement('lastmod', $p->getDate()->format('Y-m-d'));
            $xml->endElement();
        }

        $xml->endElement(); // urlset
        $xml->endDocument();
    }
}
