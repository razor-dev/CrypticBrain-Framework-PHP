<?php
/**
 * CCache is a helper class that provides a set of helper methods for caching mechanism 
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * setCacheFile
 * getCacheFile
 * deleteCacheFile
 * setCacheLifetime
 * getCacheLifetime
 * setContent
 * getContent
 */

class CCache
{
	/** The limit in amount of cache files */
	const CACHE_LIMIT = 150;

    /** @var string */
    private static $_cacheFile = '';
    /** @var integer */
    private static $_cacheLifetime = '';
    /** @var integer */
    private static $_fileLifetime = 0;

    /**
     * Setter
     * @param $cacheFile
     */
    public static function setCacheFile($cacheFile = '')
    {
        self::$_cacheFile = !empty($cacheFile) ? $cacheFile : '';
    }

    /**
     * Getter
     * @return string
     */
    public static function getCacheFile()
    {
        return self::$_cacheFile;
    }

    /**
     * Delete
     * @param string $cacheFile
     * @return string
     */
    public static function deleteCacheFile($cacheFile)
    {
        return CFile::deleteFile(CConfig::get('cache.path').$cacheFile);
    }
    
    /**
     * Sets cache life time
     * @param $cacheLifetime
     */
    public static function setCacheLifetime($cacheLifetime = 0)
    {
        self::$_cacheLifetime = !empty($cacheLifetime) ? $cacheLifetime : 0;
    }

    /**
     * Gets cache life time
     * @return integer
     */
    public static function getCacheLifetime()
    {
        return self::$_cacheLifetime;
    }

    /**
     * Sets file life time
     * @param $fileLifetime
     */
    public static function setFileLifetime($fileLifetime = 0)
    {
        self::$_fileLifetime = !empty($fileLifetime) ? $fileLifetime : 0;
    }

    /**)
     * Gets file life time
     * @return integer
     */
    public static function getFileLifetime()
    {
        return self::$_fileLifetime;
    }
    
    /**
     * Sets cache in cache file
     * @param string $content
     */
    public static function setContent($content = '')
    {
        $cacheDir = CConfig::get('cache.path');
        if(!empty(self::$_cacheFile) and !empty($content)){
            if(CFile::getDirectoryFilesNumber($cacheDir) >= self::CACHE_LIMIT){
                CFile::removeDirectoryOldestFile($cacheDir);
            }

            CFile::writeToFile(self::$_cacheFile, json_encode($content));
            self::setFileLifetime(time());
        }
    }

    /**
     * Checks if cache exists and valid and return it's content
     * @param string $cacheFile
     * @param int|string $cacheLifetime
     * @return mixed
     */
    public static function getContent($cacheFile = '', $cacheLifetime = '')
    {
        $result = '';
        $cacheContent = '';
        $cacheDir = CConfig::get('cache.path');
        
        if(!empty($cacheFile)) self::setCacheFile($cacheDir.$cacheFile);
        if(!empty($cacheLifetime)) self::setCacheLifetime($cacheLifetime);
        
        if(!empty(self::$_cacheFile) && !empty(self::$_cacheLifetime)){
            if(file_exists(self::$_cacheFile)){
                $fileLifetime = filemtime(self::$_cacheFile);
                $cacheLifetime = self::$_cacheLifetime * 60;
                if((filesize(self::$_cacheFile) > 0) && ((time() - $cacheLifetime) < $fileLifetime)){
                    ob_start();
                    include self::$_cacheFile;
                    $cacheContent = ob_get_contents();
                    ob_end_clean();

                    self::setFileLifetime($fileLifetime);
                }
                $result = !empty($cacheContent) ? json_decode($cacheContent, true) : $cacheContent;
            }
        }
        
        return $result;
    }
}