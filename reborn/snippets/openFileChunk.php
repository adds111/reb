<?php
global $modx;

if (isset($file)) {
    $executeLogFile = MODX_BASE_PATH . "/assets/reborn/cache/openFileChunk.log";

    if (!is_dir(dirname($executeLogFile))) {
        mkdir(dirname($executeLogFile), 777, true);
    }

    if (filesize($executeLogFile) > (2 * 1024 * 1024)) {
        file_put_contents($executeLogFile, '');
    }

    file_put_contents($executeLogFile, "\n Execute file: \"$file\" in date: \"" . date("H:i:s d-m-Y") . "\"\n", FILE_APPEND);

    $fullFilepath = MODX_BASE_PATH . $file;

    $fileContent = file_get_contents($fullFilepath);

    preg_match_all('#\{\{(\w+)\}\}#', $fileContent, $chunks);

    foreach ($chunks[1] as $chunkIndex => $chunk) {
        file_put_contents($executeLogFile, "\t Run chunk: \"$chunk\"\n", FILE_APPEND);

        $chunkContent = $modx->getChunk($chunk);
        $fileContent = str_replace($chunks[0][$chunkIndex], $chunkContent, $fileContent);
    }

    preg_match_all('#\[[\!|\[](\w+)[\!|\]]\]#', $fileContent, $simpleSnippets);

    foreach ($simpleSnippets[1] as $snippetIndex => $snippet) {
        file_put_contents($executeLogFile, "\t Run snippet: \"$snippet\" without params\n", FILE_APPEND);

        $snippetContent = $modx->runSnippet($snippet);
        $fileContent = str_replace($simpleSnippets[0][$snippetIndex], $snippetContent, $fileContent);
    }

    preg_match_all('#\[[\!|\[](\w+\?.*)[\!|\]]\]#', $fileContent, $hardSnippets);

    $fileinfo = pathinfo($file);

    foreach ($hardSnippets[1] as $snippetIndex => $snippet) {
        file_put_contents($executeLogFile, "\t Run hard snippet: \"$snippet\" with params:\n", FILE_APPEND);

        $snippetBlocks = explode('?', $snippet);
        $snippetName = $snippetBlocks[0];
        $snippetParameters_String = str_replace(array('`'), '', trim($snippetBlocks[1]));
        $snippetParameters_Array = array();

        $explodedParameters = explode('&', $snippetParameters_String);
        foreach (array_diff($explodedParameters, array('')) as $parameter) {
            file_put_contents($executeLogFile, "\t\t Snippet params: \"$parameter\"\n", FILE_APPEND);

            $parameterExplode = explode('=', $parameter, 2);
            $snippetParameters_Array += array(trim($parameterExplode[0]) => trim($parameterExplode[1]));
        }

        $snippetContent = $modx->runSnippet($snippetName, $snippetParameters_Array);
        $fileContent = str_replace($hardSnippets[0][$snippetIndex], $snippetContent, $fileContent);
    }

    return $fileContent;
}

return;

?>