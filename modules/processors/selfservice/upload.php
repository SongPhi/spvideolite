<?php

class SelfServiceUploadHandler extends SPVIDEO_CLASS_UploadHandler {
    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null) {
        $file = parent::handle_file_upload(
            $uploaded_file, $name, $size, $type, $error, $index, $content_range
        );
        try {
            if (empty($file->error)) {
                $token = $_POST['token'];
                $userId = OW::getUser()->getId();
                $dbo = OW::getDbo();
                $dbo->update('INSERT INTO `'.OW_DB_PREFIX.'spvideo_upl_temp` (`token`,`userId`,`isCompleted`,`filename`,`filesize`) VALUES (\''.$token.'\',\''.$userId.'\',1,\''. $file->name .'\','.$file->size.')');
                rename($this->get_upload_path($file->name), $this->get_upload_path($token));
            }
            return $file;
        } catch (Exception $e) {
            $file->error = $e->getMessage();
            return $file;
        }
        
    }
}

$upload_handler = new SelfServiceUploadHandler(array(
    'upload_dir' => SPVIDEO_DIR_USERFILES,
    'accept_file_types' => '/\.(mp4|m4v|flv|ogv|webm)$/i',
    'param_name' => 'videoClip',
    'max_file_size' => '500000000'
));


