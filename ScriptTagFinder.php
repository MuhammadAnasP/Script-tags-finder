<?php
class ScriptTagFinder
{
    public $baseUrl;
    public $invalidUrl = FALSE;

    function __construct($siteUrl)
    {
        if (filter_var($siteUrl, FILTER_VALIDATE_URL) === FALSE) {
            $this->invalidUrl = TRUE;
        }
    }

    function crawlPages($url, $depth = 5, &$i = 0)
    {
        global $data;
        $linkArray = [];
        if (($depth > 0) && ($i < 5)) {
            if (filter_var($url, FILTER_VALIDATE_URL) !== FALSE) {
                $headers = get_headers($url);
                if (stripos($headers[0], "200 OK")) {
                    $html = file_get_contents($url);
                    preg_match_all('~<a.*?href="(.*?)".*?>~', $html, $matches);
                    foreach ($matches[1] as $link) {
                        if ($link) {
                            $link = $this->setupLink($link);
                            if ($this->validateLink($link)) {
                                $linkArray[] = $link;
                                $pageNo = $i + 1;
                                $scriptTagData = $this->getScriptTags($link, "INNER PAGE: " . $pageNo);
                                if ($scriptTagData) {
                                    $data[] = $scriptTagData;
                                    $i++;
                                }
                                if ($i >= 5) {
                                    break;
                                }
                            }
                        }
                    }
                    if ($i < 5) {
                        foreach ($linkArray as $item) {
                            $data = $this->crawlPages($item, $depth - $i, $i);
                        }
                    }
                }
            }
        }
        return $data;
    }

    function getScriptTags($page, $level) // to get script tags present in the page
    {
        if (filter_var($page, FILTER_VALIDATE_URL) !== FALSE) {
            $headers = get_headers($page);
            if (stripos($headers[0], "200 OK")) {
                $content = file_get_contents($page);
                preg_match_all("#<script(.*?)</script>#i", $content, $matches); // #i for simple script, #is for actual code inside the script
                $tagData = [
                    "Level" => $level,
                    "Page" => $page,
                    "ScriptTagList" => $matches[1]
                ];
                return $tagData;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    private function setupLink($link) // result eg: input: /careers/ output: https://www.mozilor.com/careers/
    {
        $returnValue = $link;
        if (strpos($link, "http://") === false) {
            if (strpos($link, "https://") === false) {
                $returnValue = rtrim($this->baseUrl, '/') . '/' . ltrim($link, '/');
            }
        }
        return $returnValue;
    }

    private function validateLink($link) // check if the link is valid, if it is in the same domain etc.
    {
        $flag = FALSE;
        global $usedLinkArray;
        if (strpos($link, '#') === false) {
            if (strpos($link, 'javascript:void(0)') === false) {
                if ($link != $this->baseUrl) {
                    if (trim($this->baseUrl, '/') != $link) {
                        if (strpos($link, $this->baseUrl) !== false) {
                            if (!empty($usedLinkArray)) {
                                if (!in_array($link, $usedLinkArray)) {
                                    $usedLinkArray[] = $link;
                                    $flag = TRUE;
                                }
                            } else {
                                $usedLinkArray[] = $link;
                                $flag = TRUE;
                            }
                        }
                    }
                }
            }
        }
        return $flag;
    }
}
