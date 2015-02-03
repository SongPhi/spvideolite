<?php

class SPVIDEOLITE_PRO_SELFSERVICE_CLASS_UploadHandler extends SPVIDEOLITE_CLASS_UploadHandler {
    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null) {
        $file = parent::handle_file_upload(
            $uploaded_file, $name, $size, $type, $error, $index, $content_range
        );
        try {
            if (empty($file->error)) {
                $token = $_POST['token'];
                $userId = OW::getUser()->getId();
                $dbo = OW::getDbo();
                $dbo->update('INSERT INTO `'.OW_DB_PREFIX.'spvideo_upl_temp` (`token`,`userId`,`isCompleted`,`filename`,`filesize`) VALUES (\''.$token.'\',\''.$userId.'\',1,\''. addslashes($file->name) .'\','.$file->size.')');
            } else {
                throw new Exception($file->error, 1);                
            }
            return $file;
        } catch (Exception $e) {
            $file->error = $e->getMessage();
            return $file;
        }
        
    }
}