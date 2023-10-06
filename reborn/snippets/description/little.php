<?php
global $modx;

if (isset($title, $description)) {
    $descriptionLittleBody = file_get_contents(MODX_LAYOUT_PATH . "description/description-little-body.html");

    $pattern = '#descriptionLittle\?#';

    if (preg_match($pattern, $title)) {
        return null;
    }

    $descriptionPattern = '#\[\[descriptionLittle\?(.*?|\s*?|\w*?)*\]\]#';

    preg_match_all($descriptionPattern, $description, $matches);

    if ($matches[0]) {
        foreach ($matches as $match) {
            $descriptionBodyPattern = '#\[\[descriptionLittle\?(.*)\]\]#';
            $descriptionBody = preg_match($descriptionBodyPattern, $match);

            $matchParams = explode('&', $descriptionBody);
            $snippetKeys = array();

            foreach ($matchParams as $param) {
                $param = explode('=', $param, 2);
                $snippetKeys += array(trim($param[0]) => trim($param[1]));
            }

            str_replace($match, $modx->runSnippet('descriptionLittle', $snippetKeys), $description);
        }
    }

    return str_replace(array('[[+TITLE+]]', '[[+DESCRIPTION+]]'), array($title, $description), $descriptionLittleBody);
}

?>
