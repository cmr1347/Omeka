<?php 
/**
* A wrapper for file upload/transfer to the Omeka archive.
*/
class Omeka_File_Ingest
{
    protected static $_archiveDirectory = FILES_DIR;
    
    public static function upload($item, $options)
    {
        $upload = new Zend_File_Transfer_Adapter_Http($options);
        $upload->setDestination(self::$_archiveDirectory);
        
        // Add a filter to rename the file to something archive-friendly.
        $upload->addFilter(new Omeka_Filter_Filename);
        
        // Grab the info from $_FILES array (prior to receiving the files).
        $fileInfo = $upload->getFileInfo();
        
        if (!$upload->receive()) {
            throw new Omeka_Validator_Exception(join("\n\n", $upload->getMessages()));
        }
        
        $files = array();
        foreach ($fileInfo as $key => $info) {
            $file = new File;
            try {
                $file->original_filename = $info['name'];
                $file->item_id = $item->id;
                
                $file->setDefaults($upload->getFileName($key));
                
                // Create derivatives and extract metadata.
                // TODO: Move these create images / extract metadata events to 
                // the 'after_file_upload' hook whenever it becomes possible to 
                // implement hooks within core Omeka.
                //$file->createDerivatives();
                //$file->extractMetadata();
                
                $file->forceSave();
                
                fire_plugin_hook('after_upload_file', $file, $item);
                
                $files[] = $file;
                
            } catch(Exception $e) {
                if (!$file->exists()) {
                    $file->unlinkFile();
                }
                throw $e;
            }
        }
        return $files;
    }
}