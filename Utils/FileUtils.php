<?php

namespace AcMarche\Pivot\Utils;

class FileUtils
{
    public const FILE_NAME_LOG = 'load.log';
    public const FILE_NAME_XML = 'hades.xml';

    public function writeFile(string $file, array $data) {
        file_put_contents(FileUtils::FILE_NAME_LOG, implode($data));
    }

    public function readFile(string $fileName) {

    }

    public function checkIntegrityXml() {

    }
}