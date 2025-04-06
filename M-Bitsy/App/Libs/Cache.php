<?php

namespace App\Libs;


/*
* Cache
*/
class Cache
{
    private $cachedir;
    private $type;

    public function __construct()
    {
        $this->cachedir = CACHE_PATH;
        $this->type = strtolower(trim(CONFIG['CACHE_TYPE']));
        // Cache Connection
        switch ($this->type) {
            case "memcache":
                $this->obj = new Memcache;
                if (!@$this->obj->Connect(CONFIG['MEMCACHE_HOST'], CONFIG['MEMCACHE_PORT'])) {
                    $this->type = "disk";
                }
                break;
            default:
                $this->type = "disk";
        }
    }

    // Set Cache
    public function Set($var, $val, $expire = 0)
    {
        if ($expire == 0) {
            return;
        }
        switch ($this->type) {
            case "memcache":
                return $this->obj->set(CONFIG['SITENAME'] . "_" . $var, $val, 0, $expire);
                break;
            case "disk":
                $fp = fopen($this->cachedir . "/$var.cache", "w");
                fwrite($fp, serialize($val));
                fclose($fp);
                return;
                break;
        }
    }

    // Delete Cache
    public function Delete($var)
    {
        switch ($this->type) {
            case "memcache":
                return $this->obj->delete(CONFIG['SITENAME'] . "_" . $var);
                break;
            case "disk":
                @unlink($this->cachedir . "/$var.cache");
                break;
        }
    }

    // Get Cache
    public function Get($var, $expire = 0)
    {
        if ($expire == 0) {
            return false;
        }
        switch ($this->type) {
            case "memcache":
                return $this->obj->get(CONFIG['SITENAME'] . "_" . $var);
                break;
            case "disk":
                $file = $this->cachedir . "/$var.cache";
                if (file_exists($file) && (CONFIG['TT_TIME'] - filemtime($file)) < $expire) {
                    return unserialize(file_get_contents($file));
                }
                return false;
                break;
        }
    }
    
}