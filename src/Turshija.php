<?php

namespace Vasil\Turshija;

use Vasil\Turshija\Helpers\App;
use Vasil\Turshija\Helpers\Image;
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

        $photos = $this->preparePhotos();
        $posts = $this->preparePosts($photos);

        $postPhotos = array_merge([], ...array_map(fn($p) => $p->getPhotos(), $posts));
        $galleryPhotos = array_values(array_diff($photos, $postPhotos));

        $this->prepareHomepage($posts, array_map([$this, 'photoUrl'], $galleryPhotos));
        $this->prepareAssets();
        $this->prepareSitemap($posts);

        return 0;
    }

    private function preparePosts(array $photos)
    {
        $posts = glob(App::contentsDir() . '/posts/*.md');
        $postData = [];

        foreach ($posts as $p) {
            $data = Parse::content($p);

            if ($data->getDate() === null) {
                fwrite(STDERR, "Skipping $p: missing or invalid date.\n");
                continue;
            }

            $wanted = array_filter(array_map('trim', explode(',', $data->getProp('photos') ?? '')));

            foreach (array_diff($wanted, $photos) as $missing) {
                fwrite(STDERR, "$p: photo $missing not found in photos directory.\n");
            }

            $data->setPhotos(array_values(array_intersect($photos, $wanted)));

            $postData[] = $data;

            $header = Template::render('header.php', [
                'title' => $this->websiteData->getProp('title')
            ]);
            $footer = Template::render('footer.php');
            $post = Template::render('post.php', [
                'post' => $data->getHtml(),
                'title' => $data->getProp('title'),
                'photos' => array_map([$this, 'photoUrl'], $data->getPhotos()),
            ]);

            $final = Template::render('index.php', [
                'header' => $header,
                'footer' => $footer,
                'content' => $post,
                'title' => $data->getProp('title') . ' - ' . $this->websiteData->getProp('title'),
                'website' => $this->websiteData,
            ]);

            File::save(App::exportDir() . $data->getUrl() . '/index.html', $final);
        }

        // Newest first
        usort($postData, fn($a, $b) => $b->getDate() <=> $a->getDate());

        return $postData;
    }

    private function preparePhotos(): array
    {
        $names = [];

        foreach (glob(App::contentsDir() . '/photos/*') as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (!is_file($file) || !in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'])) {
                continue;
            }

            $name = basename($file);
            Image::resize($file, App::exportDir() . '/assets/photos/' . $name, 1000);
            $names[] = $name;
        }

        usort($names, fn($a, $b) => strnatcasecmp($b, $a));

        return $names;
    }

    private function photoUrl(string $name): string
    {
        return '/assets/photos/' . rawurlencode($name);
    }

    private function prepareHomepage(array $posts, array $photos)
    {
        $header = Template::render('header.php', [
            'title' => $this->websiteData->getProp('title')
        ]);
        $footer = Template::render('footer.php');
        $homepage = Template::render('homepage.php', ['posts' => $posts, 'photos' => $photos]);

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
        $dest = App::exportDir() . '/assets';

        File::copyDir(App::root() . '/templates/default/assets', $dest);
        File::copyDir(App::contentsDir() . '/assets', $dest);
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
            $xml->writeElement('lastmod', $p->getW3cDate());
            $xml->endElement();
        }

        $xml->endElement(); // urlset
        $xml->endDocument();
    }
}
