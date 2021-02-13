<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

class ParseService
{
    public function store(Request $request): JsonResponse
    {
        if ($request->hasFile('check')) {
            $file = $this->fileUpload($request);
            $data = $this->format($file);
            $html = $this->getMainHtml($data);
            $products = $this->extractProduct($html);
            $info = $this->getData(str_replace($products, '', $html));
            $products = $this->getData($products);
            return response()->json(['info' => $info, 'products' => $products], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json("Har file not found in this request.");
        }
    }

    public function fileUpload($request)
    {
        $file = $request->file('check');
        $file->move('har/', 'check.har');
        return $file;
    }

    public function format($file)
    {
        $data = file_get_contents('har/check.har');
        $data = trim($data);
        $data = str_replace('\"', '"', $data);
        $data = str_replace('\r\n', '', $data);
        $data = str_replace('\n', '', $data);

        return $data;
    }

    public function getMainHtml($data)
    {
        $crawler = new Crawler($data);
        $ifw_card = $crawler->filter('.ifw-card-body')->first();
        if ($ifw_card->count() > 0) {
            return $ifw_card->html();
        } else {
            return "Content reading error.";
        }
    }

    public function extractProduct($html)
    {
        $start = stripos($html, '<!-- Products -->');
        $end = stripos($html, '<!-- /Products -->');

        return substr($html, $start, (((int)$end) - (int)$start));
    }

    public function getData($data)
    {
        $data = new Crawler($data);

        $data = $data->filter('.ifw-cols');

        $keys = new Collection();
        $data->filter('.text-left')->each(function (Crawler $node) use ($keys) {
            return $keys->push(trim($node->text()));
        });

        /* FIXME: Don't cut the element for combine */
        $keys = $keys->reject(function ($value) {
            return stripos($value, 'в т.ч. налоги') > 0;
        });

        $values = new Collection();
        $data->filter('.text-right')->each(function (Crawler $node) use ($values) {
        if (preg_match('/ (\s*X\s*|\s*=\s*) /', $node->text())) {
            list($quantity, $price, $total_price) = preg_split('/ (\s*X\s*|\s*=\s*) /', $node->text());
            $node = collect([
                ['quantity' => $quantity, 'price' => $price, 'total_price' => $total_price],
            ])->map(function ($row) {
                return collect($row);
            });

            $node[0]->toJson();

        } else {
            return $values->push(trim($node->text()));
        }
            return $values->push($node);
        });


        return $keys->combine($values)->all();
    }

}
