<?php
$files = array_diff(scandir(MODX_ENCORE_BUILD_PATH), array('.', '..'));

if (!is_dir(MODX_ENCORE_BUILD_PATH)) {
    throw new Exception("Encore folder is does not exist.");
}

if (count($files) < 1) {
    throw new Exception("Encore folder is empty.");
}
if (!isset($only)) {
    $only = "";
}

foreach ($files as $file) {
    if (preg_match('#.+(\.\w+)#', $file, $match)) {
        if (isset($match[1])) {
            $filepath = MODX_ENCORE_BUILD_URL . $file;

            switch ($match[1]) {
                case '.js' : {
                    if (!empty($only)) {
                        if ($match[1] != ".$only") {
                            break;
                        }
                    }

                    echo "<script type='text/javascript' src='$filepath' defer></script>" . "\t";
                    break;
                }
                case '.css' : {
                    if (!empty($only)) {
                        if ($match[1] != ".$only") {
                            break;
                        }
                    }

                    echo "<link rel='stylesheet' href='$filepath'>" . "\t";
                    break;
                }
            }
        }
    }
}

return null;
?>