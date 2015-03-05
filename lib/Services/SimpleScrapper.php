<?php
namespace Services;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class SimpleScrapper{
    private $base_url;
    private $site_links;
    private $max_depth;
    private $stopUrls;
    private $cssPaths = array();
    private $extractInfo = true;
    private $objectsRoot = false;


    public function __construct($base_url, $cssPaths = false, $max_depth = 10) {
        if (strpos($base_url, 'http') === false) { // http protocol not included, prepend it to the base url
            $base_url = 'http://' . $base_url;
        }

        $this->base_url = $base_url;
        $this->site_links = array();
        $this->max_depth = $max_depth;
        $this->cssPaths = $cssPaths;
        $this->stopUrls = array();
    }

    /**
     * checks the uri if can be crawled or not
     * in order to prevent links like "javascript:void(0)" or "#something" from being crawled again
     * @param string $uri
     * @return boolean
     */
    protected function checkIfCrawlable($uri) {
        if (empty($uri)) {
            return false;
        }

        $stop_links = array(//returned deadlinks
            '@^javascript\:void\(0\)$@',
            '@^#.*@',
        );

        foreach ($stop_links as $ptrn) {
            if (preg_match($ptrn, $uri)) {
                return false;
            }
        }

        return true;
    }

    /**
     * normalize link before visiting it
     * currently just remove url hash from the string
     * @param string $uri
     * @return string
     */
    protected function normalizeLink($uri) {
        $uri = preg_replace('@#.*$@', '', $uri);

        return $uri;
    }

    public function setStopUrls($urls){
        $this->stopUrls = $urls;
    }
    public function setObjectsRoot($rootElement){
        $this->objectsRoot = $rootElement;
    }

    public function setExtractInfo($extract = false){
        $this->extractInfo = $extract;
    }

    private function checkForStopUrl($url_to_traverse){
        foreach($this->stopUrls as $stopUrl){
//            echo "Macthing $url_to_traverse = $stopUrl\n";
            if(Util::strContains($url_to_traverse, $stopUrl)){
                return true;
            }
        }
        return false;
    }

    /**
     * initiate the crawling mechanism on all links
     * @param string $url_to_traverse
     */
    public function traverse($url_to_traverse = null, $content = null) {
        if (is_null($url_to_traverse)) {
            $url_to_traverse = $this->base_url;
            if($this->checkForStopUrl($url_to_traverse)){
                return false;
            }
            $this->site_links[$url_to_traverse] = array(//initialize first element in the site_links
                'links_text' => array("BASE_URL"),
                'absolute_url' => $url_to_traverse,
                'frequency' => 1,
                'visited' => false,
                'external_link' => false,
                'original_urls' => array($url_to_traverse),
            );
        } else if($this->checkForStopUrl($url_to_traverse)){
            return false;
        }
        $this->_traverseSingle($url_to_traverse, $content, $this->max_depth);
        return true;
    }

    /**
     * crawling single url after checking the depth value
     * @param string $url_to_traverse
     * @param int $depth
     */
    protected function _traverseSingle($url_to_traverse, $content = null, $depth = 1) {
        //echo $url_to_traverse . chr(10);

        try {
            $client = new Client();
            if($content){
                $crawler = new Crawler(null, $url_to_traverse);
                $crawler->addContent($content, 'text/html');
                $status_code = 200;
            } else {
                $crawler = $client->request('GET', $url_to_traverse);

                $status_code = $client->getResponse()->getStatus();
            }
            $this->site_links[$url_to_traverse]['status_code'] = $status_code;

            if ($status_code == 200) { // valid url and not reached depth limit yet
                $content_type = $client->getResponse()->getHeader('Content-Type');
                if (strpos($content_type, 'text/html') !== false) { //traverse children in case the response in HTML document

                    if($this->extractInfo){
                        $this->extractTitleInfo($crawler, $url_to_traverse);
                        $this->extractMicrodata($crawler, $url_to_traverse);
                        $this->extractImages($crawler, $url_to_traverse);
                        $this->extractMetatags($crawler, $url_to_traverse);
                        $this->extractBodyInfo($crawler, $url_to_traverse);
                        $this->extractSubTitleInfo($crawler, $url_to_traverse);
                        $this->extractTextPanelsInfo($crawler, $url_to_traverse);
                        $this->elementsToObjs($crawler, $url_to_traverse);
                    }
                    $this->extractCSSPaths($crawler, $url_to_traverse);


                    $current_links = array();
                    if (@$this->site_links[$url_to_traverse]['external_link'] == false) { // for internal uris, get all links inside
//                        $current_links = $this->extractLinksInfo($crawler, $url_to_traverse);
                    }

                    $this->site_links[$url_to_traverse]['visited'] = true; // mark current url as visited
//                    $this->traverseChildLinks($current_links, $depth - 1);
                }
            }
        } catch (CurlException $ex) {
            error_log("CURL exception: " . $url_to_traverse);
            $this->site_links[$url_to_traverse]['status_code'] = '404';
        } catch (\Exception $ex) {
            error_log("error retrieving data from link: " . $url_to_traverse);
            $this->site_links[$url_to_traverse]['status_code'] = '404';
        }
    }

    /**
     * after checking the depth limit of the links array passed
     * check if the link if the link is not visited/traversed yet, in order to traverse
     * @param array $current_links
     * @param int $depth
     */
    protected function traverseChildLinks($current_links, $depth) {
        if ($depth <= 0) {
            return;
        }

        foreach ($current_links as $uri => $info) {
            if (!isset($this->site_links[$uri])) {
                $this->site_links[$uri] = $info;
            } else{
                $this->site_links[$uri]['original_urls'] = isset($this->site_links[$uri]['original_urls'])?array_merge($this->site_links[$uri]['original_urls'], $info['original_urls']):$info['original_urls'];
                $this->site_links[$uri]['links_text'] = isset($this->site_links[$uri]['links_text'])?array_merge($this->site_links[$uri]['links_text'], $info['links_text']):$info['links_text'];
                if(@$this->site_links[$uri]['visited']) { //already visited link)
                    $this->site_links[$uri]['frequency'] = @$this->site_links[$uri]['frequency'] + @$info['frequency'];
                }
            }

            if (!empty($uri) &&
                !$this->site_links[$uri]['visited'] &&
                !isset($this->site_links[$uri]['dont_visit'])
            ) { //traverse those that not visited yet
                $this->_traverseSingle($this->normalizeLink($current_links[$uri]['absolute_url']), $depth);
            }
        }
    }

    /**
     * extracting all <a> tags in the crawled document,
     * and return an array containing information about links like: uri, absolute_url, frequency in document
     * @param Crawler $crawler
     * @param string $url_to_traverse
     * @return array
     */
    protected function extractLinksInfo(Crawler &$crawler, $url_to_traverse) {
        $current_links = array();
        $crawler->filter('a')->each(function(Crawler $node, $i) use (&$current_links) {
            $node_text = trim($node->text());
            $node_url = $node->attr('href');
            $hash = $this->normalizeLink($node_url);

            if (!isset($this->site_links[$hash])) {
                $current_links[$hash]['original_urls'][$node_url] = $node_url;
                $current_links[$hash]['links_text'][$node_text] = $node_text;

                if (!$this->checkIfCrawlable($node_url)){

                }elseif (!preg_match("@^http(s)?@", $node_url)) { //not absolute link
                    $current_links[$hash]['absolute_url'] = $this->base_url . $node_url;
                } else {
                    $current_links[$hash]['absolute_url'] = $node_url;
                }

                if (!$this->checkIfCrawlable($node_url)) {
                    $current_links[$hash]['dont_visit'] = true;
                    $current_links[$hash]['external_link'] = false;
                } elseif ($this->checkIfExternal($current_links[$hash]['absolute_url'])) { // mark external url as marked
                    $current_links[$hash]['external_link'] = true;
                } else {
                    $current_links[$hash]['external_link'] = false;
                }
                $current_links[$hash]['visited'] = false;

                $current_links[$hash]['frequency'] = isset($current_links[$hash]['frequency']) ? $current_links[$hash]['frequency']++ : 1; // increase the counter
            }

        });

        if (isset($current_links[$url_to_traverse])) { // if page is linked to itself, ex. homepage
            $current_links[$url_to_traverse]['visited'] = true; // avoid cyclic loop
        }
        return $current_links;
    }

    /**
     * extract information about document title, and h1
     * @param Crawler $crawler
     * @param string $uri
     */
    protected function extractTitleInfo(Crawler &$crawler, $url) {
        $this->site_links[$url]['title'] = trim($crawler->filterXPath('html/head/title')->text());

        $h1_count = $crawler->filter('h1')->count();
        $this->site_links[$url]['h1_count'] = $h1_count;
        $this->site_links[$url]['h1_contents'] = array();

        if ($h1_count) {
            $crawler->filter('h1')->each(function(Crawler $node, $i) use($url) {
                $this->site_links[$url]['h1_contents'][$i] = trim($node->text());
            });
        }
    }

    protected function extractImages(Crawler &$crawler, $url) {
        $count = $crawler->filter('img')->count();
        $this->site_links[$url]['img_count'] = $count;
        $this->site_links[$url]['images'] = array();

        if ($count) {
            $crawler->filter('img')->each(function(Crawler $node, $i) use($url) {
                if(trim($node->attr('src'))){
                    $this->site_links[$url]['images'][$i]['src'] = trim($node->attr('src'));
                    $this->site_links[$url]['images'][$i]['alt'] = trim($node->attr('alt'));
                }
            });
        }
    }

    protected function extractSubTitleInfo(Crawler &$crawler, $url) {
        //h2
        $h2_count = $crawler->filter('h2')->count();
        $this->site_links[$url]['h2_count'] = $h2_count;
        $this->site_links[$url]['h2_contents'] = array();

        if ($h2_count) {
            $crawler->filter('h2')->each(function(Crawler $node, $i) use($url) {
                $this->site_links[$url]['h2_contents'][$i] = trim($node->text());
            });
        }
        //h3
        $h3_count = $crawler->filter('h3')->count();
        $this->site_links[$url]['h3_count'] = $h3_count;
        $this->site_links[$url]['h3_contents'] = array();

        if ($h3_count) {
            $crawler->filter('h3')->each(function(Crawler $node, $i) use($url) {
                $this->site_links[$url]['h3_contents'][$i] = trim($node->text());
            });
        }
    }

    protected function extractTextPanelsInfo(Crawler &$crawler, $url) {
        //p
        $p_count = $crawler->filter('p')->count();
        $this->site_links[$url]['p_count'] = $p_count;
        $this->site_links[$url]['p_contents'] = array();

        if ($p_count) {
            $crawler->filter('p')->each(function(Crawler $node, $i) use($url) {
                $this->site_links[$url]['p_contents'][$i] = trim($node->text());
            });
        }
        //code
        $code_count = $crawler->filter('code')->count();
        $this->site_links[$url]['code_count'] = $code_count;
        $this->site_links[$url]['code_contents'] = array();

        if ($code_count) {
            $crawler->filter('code')->each(function(Crawler $node, $i) use($url) {
                $this->site_links[$url]['code_contents'][$i] = trim($node->text());
            });
        }
        //pre
        $code_count = $crawler->filter('pre')->count();
        $this->site_links[$url]['pre_count'] = $code_count;
        $this->site_links[$url]['pre_contents'] = array();

        if ($code_count) {
            $crawler->filter('pre')->each(function(Crawler $node, $i) use($url) {
                $this->site_links[$url]['pre_contents'][$i] = trim($node->text());
            });
        }
    }

    /**
     * extract information about document body, and h1
     * @param Crawler $crawler
     * @param string $uri
     */
    protected function extractBodyInfo(Crawler &$crawler, $url) {
        $this->site_links[$url]['body']['text'] = trim($crawler->filterXPath('html/body')->text());
        $this->site_links[$url]['html'] = trim($crawler->filterXPath('html')->html());
    }

    /**
     * extract information about document body, and h1
     * @param Crawler $crawler
     * @param string $uri
     */
    protected function extractMicrodata(Crawler &$crawler, $url) {
        $r = new MicrodataService($url, trim($crawler->filterXPath('html')->html()));
        $data = $r->read();
        $array = json_decode(json_encode($data),true);
        $this->site_links[$url]['microdata'] = $array;
    }

    /**
     * extract information about document body, and h1
     * @param Crawler $crawler
     * @param string $uri
     */
    protected function extractMetatags(Crawler &$crawler, $url) {
        $this->site_links[$url]['metatags'] = $this->getHtmlMetaData(trim($crawler->filterXPath('html')->html()));
    }

    protected function extractCSSPaths(Crawler &$crawler, $url) {
        if($this->cssPaths)
            foreach($this->cssPaths as $cssPath){
                if(isset($cssPath['name']) && isset($cssPath['path'])){
                    $children = isset($cssPath['children']) ? $cssPath['children'] : false;
                    $regex = isset($cssPath['regex']) ? $cssPath['regex'] : false;
                    foreach($cssPath['path'] as $path){
                        $this->extractCSSPath($crawler, $url, $cssPath['name'], $path, $children, $regex);
                    }
                }
            }
    }

    protected function extractCSSPath(Crawler &$crawler, $url, $name, $cssPath, $children = false, $regex = false) {
        $path = $cssPath;
        $count = $crawler->filter($path)->count();
        $this->site_links[$url][$name.'_count'] = $count;
        $this->site_links[$url][$name] = array();
        if ($count) {
            $crawler->filter($path)->each(function(Crawler $node, $i) use($url, $name, $children, $regex) {
                //if children null den go into this otherwise go for childrens
                if($children){
                    $this->getChildrenData($children, $name, $i, $url, $node);
                } else
                    $this->site_links[$url][$name][] = array(
                        'text' => $regex ? preg_replace($regex, "", trim($node->text())) : trim($node->text()),
                        'html' => trim($node->html()),
                        'href' => trim($node->attr('href'))
//                    'data-href' => trim($node->attr('data-href'))
                    );
            });
        }
    }

    protected function getChildrenData($children, $parent, $parentIndex, $url, $crawler){
        if($children){
            $paths = $children;
            $data = array();
            foreach($paths as $cssPath){
                if(isset($cssPath['name']) && isset($cssPath['path'])){
                    $children = isset($cssPath['children']) ? $cssPath['children'] : false;
                    $regex = isset($cssPath['regex']) ? $cssPath['regex'] : false;
                    foreach($cssPath['path'] as $path){
                        $this->extractCSSPathChild($crawler, $parent, $parentIndex, $url, $cssPath['name'], $path, $children, $regex);
                    }
                }
            }
            return $data;
        }
        return null;
    }

    protected function extractCSSPathChild(Crawler &$crawler, $parent, $parentIndex, $url, $name, $cssPath, $children = false, $regex = false) {
        $path = $cssPath;
        $count = $crawler->filter($path)->count();
        $this->site_links[$url][$parent][$parentIndex][$name] = array();
        $data = array();
        if ($count) {
            $crawler->filter($path)->each(function(Crawler $node, $i) use($url, $parent, $parentIndex, $name, $children, $data, $regex) {
                $this->site_links[$url][$parent][$parentIndex][$name] = array(
                    'text' => $regex ? preg_replace($regex, "", trim($node->text())) : trim($node->text()),
                    'html' => trim($node->html()),
                    'href' => trim($node->attr('href')),
//                    'children' => $this->getChildrenData($children, $name, $url, $node)
//                    'data-href' => trim($node->attr('data-href'))
                );
            });
        }
        return $data;
    }

    public function elementToObj(Crawler &$crawler) {
        $u = "";
        $results = $crawler->each(function(Crawler $node, $i) use($u) {
            $data[$node->nodeName()][$i] = array(
                'text' => trim($node->text()),
                'href' => trim($node->attr('href')),
//                'children' => $this->elementToObj($node)
//                    'data-href' => trim($node->attr('data-href'))
            );
            return $data;
        });
        return $results;
    }


    public function elementsToObjs(Crawler &$crawler, $url){
        //get section main
        // pass section breaker
        if($this->objectsRoot){
            $dom = new \DOMDocument();
            $node = $crawler->filter($this->objectsRoot);
//            print_r($node);
            $s = $dom->loadHTML($node->html());
//            $dom->loadXML($dom->saveXML());
//            print_r($dom);
            $data = $this->elementToObjn($dom->documentElement, $node);
            $this->site_links[$url]['objects'] = $data;
        }
    }

    public function elementToObjn($element, Crawler $crawler, $path = '', $childNumber = 0) {
        try{
            if(isset($element->tagName)){
//                echo $element->tagName, "\n";
                if($element->tagName != "html" && $element->tagName != "body"){
                    $path = $path ? $path ." > " : "";
                    $path = $path.$element->tagName;// . (isset($obj['class']) ? "." . $obj['class'] : "");
                    $childNumber++;
                }
                $spath = $path ? $path.":nth-child($childNumber)" : $path;

                $text = "";
                $obj = array(
                    "tag" => $element->tagName,
                    "path" => $spath,
                    "text" => $text
                );
                if(isset($element->attributes))
                    foreach ($element->attributes as $attribute) {
                        $obj[$attribute->name] = $attribute->value;
                    }
                $i = 0;
                if($element->hasChildNodes())
                    foreach ($element->childNodes as $subElement) {
                        if ($subElement->nodeType == XML_TEXT_NODE) {
                            $subNode = $crawler->filter($spath);
                            echo $spath,"\n";
                            $obj['text'] = $subNode->text();
                        } elseif ($subElement->nodeType == XML_CDATA_SECTION_NODE) {
                            $obj['text'] = $subElement->data;
                        } else{
                            $response = $this->elementToObjn($subElement, $crawler, $spath, $i);
                            if($response){
                                $obj["children"][] = $response;
                                $i++;
                            }
                        }
                    }
                return $obj;
            }
            return false;
        }  catch(\Exception $ex){
//            \G::$logger->Log($ex, "Error");
//            throw $ex;
            print_r($ex->getMessage());
            return array();
        }
    }

    /**
     * getting information about links crawled
     * @return array
     */
    public function getLinksInfo() {
        return $this->site_links;
    }

    /**
     * check if the link leads to external site or not
     * @param string $url
     * @return boolean
     */
    public function checkIfExternal($url) {
        $base_url_trimmed = str_replace(array('http://', 'https://'), '', $this->base_url);

        if (preg_match("@http(s)?\://$base_url_trimmed@", $url)) { //base url is not the first portion of the url
            return false;
        } else {
            return true;
        }
    }

    public function getHtmlMetaData($html)
    {
        $result = false;
        $contents = $this->getUrlContents($html);
        $metaTags = null;
        if (isset($contents) && is_string($contents))
        {
            $title = null;
            $metaTags = null;
            preg_match('/<title>([^>]*)<\/title>/si', $contents, $match );
            if (isset($match) && is_array($match) && count($match) > 0)
            {
                $title = strip_tags($match[1]);
            }
            preg_match_all('/<[\s]*meta[\s]*name="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $contents, $match);
            if (isset($match) && is_array($match) && count($match) == 3)
            {
                $originals = $match[0];
                $names = $match[1];
                $values = $match[2];
                if (count($originals) == count($names) && count($names) == count($values))
                {
                    $metaTags = array();
                    for ($i=0, $limiti=count($names); $i < $limiti; $i++)
                    {
                        $metaTags[strtolower($names[$i])] = $values[$i];
                    }
                }
            }
            $result = array (
                'title' => $title,
                'metaTags' => $metaTags
            );
        }

        return $metaTags;
    }

    public function getUrlContents($contents, $maximumRedirections = null, $currentRedirection = 0)
    {
        $result = "";
//        $contents = @file_get_contents($url);
        // Check if we need to go somewhere else
        if (isset($contents) && is_string($contents))
        {
            preg_match_all('/<[\s]*meta[\s]*http-equiv="?REFRESH"?' . '[\s]*content="?[0-9]*;[\s]*URL[\s]*=[\s]*([^>"]*)"?' . '[\s]*[\/]?[\s]*>/si', $contents, $match);
            if (isset($match) && is_array($match) && count($match) == 2 && count($match[1]) == 1)
            {
                $result = "";
            }
            else
            {
                $result = $contents;
            }
        }
        return $result;
    }

}