<?php

use Exceptions\ValidationException;

class FileHandler{

    public function __construct(){
        
    }

    public function isUpload($fileInputName){
        return is_uploaded_file($_FILES[$fileInputName]['tmp_name']);
    }

    public function getSavedFilePath($filename){
        $destDir = rtrim(ORDER_QUEUE_FILE_STORAGE_PATH, '\\/');

        return $destDir . '/' . ltrim($filename, '\\/');
    }

    public function getSavedFileContent($filename){
        $destDir = rtrim(ORDER_QUEUE_FILE_STORAGE_PATH, '\\/');

        $filepath = $destDir . '/' . ltrim($filename, '\\/');
        if(!file_exists($filepath)){
            throw new RuntimeException('Не удалось найти файл ' . $filename);
        }

        return file_get_contents($filepath);
    }

    public function unlinkSavedFile($filename){
        $destDir = rtrim(ORDER_QUEUE_FILE_STORAGE_PATH, '\\/');

        $filepath = $destDir . '/' . ltrim($filename, '\\/');

        return unlink($filepath);
    }

    public function moveToStorage($fileInputName){
        if(!$this->isUpload($fileInputName)){
            throw new RuntimeException('Попытка перенести незагруженный файл');
        }

        $this->validateInput($fileInputName);

        $destDir = rtrim(ORDER_QUEUE_FILE_STORAGE_PATH, '\\/');

        $fileSrcPath = $_FILES[$fileInputName]['tmp_name'];
        $extension = pathinfo($_FILES[$fileInputName]['name'], PATHINFO_EXTENSION);
        $fileUniqueName = sprintf('%d_%d.%s', time(), uniqid(), $extension);

        if(!move_uploaded_file($fileSrcPath, $destDir . '/' . $fileUniqueName)){
            throw new RuntimeException('Не удалось переместить файл, загруженный пользователем');
        }

        return $fileUniqueName;
    }

    private function validateInput($fileInputName){

        if ($_FILES[$fileInputName]['error'] != 0 || $_FILES[$fileInputName]['size'] == 0) {
            throw new ValidationException('
                <a name="error">
                    Ошибка загрузки файла. Пожалуйста, убедитесь что файл не 
                    превышает заданное ограничение на размер - не более 10 мбайт.
                </a>
            ');
        }
    
        if ($_FILES[$fileInputName]['size'] > 10485760) {
            throw new ValidationException('
                <a name="error">
                    Вы пытаетесь загрузить файл размером более 10 мбайт. 
                    Пожалуйста, соблюдайте правила загрузки файлов: размер не должен 
                    превышать 10 мбайт.
                </a>
            ');
        }
    }
  
}
