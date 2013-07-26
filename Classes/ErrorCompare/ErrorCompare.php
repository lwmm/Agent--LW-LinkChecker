<?php

/**
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package agent_linkchecker
 */

namespace AgentLinkChecker\Classes\ErrorCompare;

class ErrorCompare
{

    protected $commandHandler;
    protected $queryHandler;

    public function __construct($db)
    {
        $this->commandHandler = new \AgentLinkChecker\Classes\ErrorCompare\DataHandler\CommandHandler($db);
        $this->queryHandler = new \AgentLinkChecker\Classes\ErrorCompare\DataHandler\QueryHandler($db);
    }

    public function execute($actualErrorUrlArray, $oldErrorUrlArray)
    {
        #print_r($actualErrorUrlArray);die();
        $fixedErrors = array();

        foreach ($oldErrorUrlArray["errors"] as $errorUrl) { #auf behobene fehler pruefen

            $exists = false;

            foreach ($actualErrorUrlArray["errors"] as $newErrorUrl) {
                if ($errorUrl["id"] == $newErrorUrl["id"]) {
                    $exists = true;
                }
            }

            if (!$exists) {
                $fixedErrors[] = $errorUrl;
            }
        }
                
        $this->addPageCommentNoticeIfNotExisting($actualErrorUrlArray["errors"]);
        $this->deletePageCommentNotice($fixedErrors);

        #[[LinkChecker: item_id => 1, description => google, url => www.google.de]]
    }

    private function addPageCommentNoticeIfNotExisting($existingErrors)
    {
        foreach ($existingErrors as $error) {
            $pageComment = $this->queryHandler->getPageCommentById($error["page_id"]);

            if (strpos($pageComment, '[[LinkChecker:')) {
                if (strpos($pageComment, "item_id => " . $error["id"])) {

                    $tempComment = $pageComment;
                    $tempStringArray = array();

                    do {
                        $linkCheckerString = substr($tempComment, strpos($tempComment, '[[LinkChecker:'), strpos($tempComment, "]]") + 2);
                        $tempStringArray[] = trim(str_replace(']]', '', str_replace('[[LinkChecker:', '', $linkCheckerString)));

                        $tempComment = str_replace($linkCheckerString, "", $tempComment);
                    } while (strstr($tempComment, '[[LinkChecker:'));

                    foreach ($tempStringArray as $preparedString) {

                        $elements = explode(",", $preparedString);

                        $temp = array();
                        
                        
                        foreach ($elements as $element) {
                            $element = trim($element);

                            $explodedElement = explode("=>", $element);
                            $temp[trim($explodedElement[0])] = trim($explodedElement[1]);
                        }

                        if ($temp["item_id"] == $error["id"]) {
                            if (strstr($error["url"], '[[intern:')) {
                                $url = $error["pid"];
                            }
                            else {
                                $url = $error["url"];
                            }
                            
                            if ($temp["description"] != $error["description"] || $temp["url"] != $url) {

                                $pageComment = str_replace("\n[[LinkChecker: item_id => " . $temp["item_id"] . ", description => " . $temp["description"] . ", url => " . $temp["url"] . " ]]", "\n[[LinkChecker: item_id => " . $error["id"] . ", description => " . $error["description"] . ", url => " . $url . " ]]", $pageComment);
                            }
                            $this->commandHandler->savePageCommentById($error["page_id"],$pageComment);
                        }
                    }
                }
                else {
                    $this->appendPageCommentNotice($pageComment, $error);
                }
            }
            else {
                $this->appendPageCommentNotice($pageComment, $error);
            }
        }
    }

    private function appendPageCommentNotice($pageComment, $error)
    {
        if (strstr($error["url"], '[[intern:')) {
            $url = $error["pid"];
        }
        else {
            $url = $error["url"];
        }

        $pageComment .= "\n[[LinkChecker: item_id => " . $error["id"] . ", description => " . $error["description"] . ", url => " . $url . " ]]";
        $this->commandHandler->savePageCommentById($error["page_id"], $pageComment);
    }

    private function deletePageCommentNotice($fixedErrors)
    {
        foreach($fixedErrors as $fixedError){
            
            if (strstr($fixedError["url"], '[[intern:')) {
                $url = $fixedError["pid"];
            }
            else {
                $url = $fixedError["url"];
            }
            
            $pageComment = $this->queryHandler->getPageCommentById($fixedError["page_id"]);
            
            $pageComment = str_replace("\n[[LinkChecker: item_id => " . $fixedError["id"] . ", description => " . $fixedError["description"] . ", url => " . $url . " ]]", "", $pageComment);
            
            $this->commandHandler->savePageCommentById($fixedError["page_id"], $pageComment);
        }
    }

}