<?php

class Autoloader{
  
  /**
     * 类库自动加载，写死路径，确保不加载其他文件。
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class) {
        $name = $class;
        if(false !== strpos($name,'\\')){
          $name = strstr($class, '\\', true);
        }

        $filename = APP_FILE."use/alisdk/"."/top/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = APP_FILE."use/alisdk/"."/top/request/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = APP_FILE."use/alisdk/"."/top/domain/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = APP_FILE."use/alisdk/"."/aliyun/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = APP_FILE."use/alisdk/"."/aliyun/request/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = APP_FILE."use/alisdk/"."/aliyun/domain/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        } 
        echo $filename;       
    }
}
spl_autoload_register('Autoloader::autoload');
?>