<?php

/**
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package agent_linkchecker
 */

namespace AgentLinkChecker\Classes\LinkCheck;

class LinkCheck
{

    public static $instance = NULL;
    protected $db;

    protected function __construct($db)
    {
        $this->db = $db;
    }

    public static function getInstance($db)
    {
        if (!isset(self::$instance) || self::$instance == null) {
            self::$instance = new \AgentLinkChecker\Classes\LinkCheck\LinkCheck($db);
        }
        return self::$instance;
    }

    public function check()
    {
        $queryHandler = new \AgentLinkChecker\Classes\LinkCheck\DataHandler\QueryHandler($this->db);
        $entries = $queryHandler->getAllUrls();
        
        $error_urls = array();
        $tempArray_checkedUrls = array();

        foreach ($entries as $entry) {
            $error = false;
            $identifier = $entry["url"];
            if ($tempArray_checkedUrls[$identifier] != 1) {
                if (strstr($entry["url"], '[[intern:')) {
                    $dummy = str_replace('[[intern:', '', $entry["url"]);
                    $pid = str_replace(']]', '', $dummy);
                    $pid = intval(trim($pid));
                    if ($pid > 0) {
                        $page = $queryHandler->getPageById($pid);
                        if ($page['id'] != $pid) {
                            $temp = $entry;
                            $temp["pid"] = $pid;
                            $error_urls[] = $temp;
                        }
                    }
                    else {
                        $temp = $entry;
                        $temp["pid"] = $pid;
                        $error_urls[] = $temp;
                    }
                }
                elseif (!$this->httpFileExists($entry["url"])) {
                    $error_urls[] = $entry;
                }
                $tempArray_checkedUrls[$identifier] = 1;
            }
        }
        return array(
            "scan_date" => date("YmdHis"),
            "errors" => $error_urls
                );
    }

    private function httpFileExists($url)
    {
        $url_p = array();
        $url_p = parse_url($url);
        $host = $url_p['host'];
        $port = isset($url_p['port']) ? $url_p['port'] : 80;
        if (!$url_p['host']) {
            return false;
        }

        $fp = @fsockopen($url_p['host'], $port, $errno, $errstr, 3);

        if (!$fp) {
            //fclose ($fp);
            return false;
        }

        fputs($fp, 'GET ' . $url_p['path'] . ' HTTP/1.1' . chr(10));
        fputs($fp, 'HOST: ' . $url_p['host'] . chr(10));
        fputs($fp, 'Connection: close' . chr(10) . chr(10));
        $response = fgets($fp, 1024); //nur 1. zeile holen...

        fclose($fp);
        return !stristr($response, 'HTTP 404');
    }

}