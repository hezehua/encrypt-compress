<?php
namespace Hezehua\EncryptCompress;

class ZipCompress
{
    private function __construct(){}

    public static function zip($origin_file, $output_file = '', $password = null)
    {
        if(!file_exists($origin_file))
        {
            throw new \Exception('源文件无法访问');
        }

        //未设置输出文件名
        if(!$output_file)
        {
            $output_file = dirname($origin_file).'/'.pathinfo($origin_file)['filename'].'.zip';
        }
        else
        {
            //只设置了文件名未设置目录
            if(dirname($output_file) == '.')
            {
                $output_file = dirname($origin_file).'/'.$output_file;
            }
            //只设置了目录未设置文件名
            elseif(is_dir($output_file))
            {
                $output_file = $output_file.'/'.pathinfo($origin_file)['filename'].'.zip';
            }
        }

        if(!self::validatePath(dirname($output_file)))
        {
            throw new \Exception('输出文件夹无法访问。 output_path : '.$output_file);
        }

        if(!self::validateExtension(basename($output_file)))
        {
            throw new \Exception('输出文件格式错误');
        }

        if($password)
        {
            return `zip --password  "$password"  {$output_file} {$origin_file}`;
        }

        return `zip {$output_file}  {$origin_file}`;
    }

    //TODO
    public static function encrypt()
    {

    }

    /**
     * 校验路径
     * @param $path
     * @return string
     * @throws \Exception
     */
    private static function validatePath($path)
    {
        if(!is_dir($path))
        {
            if(is_file($path))
            {
                $path = dirname($path);
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }

        return $path;
    }

    /**
     * 校验路径
     * @param $path
     * @return string
     * @throws \Exception
     */
    private static function validateExtension($file)
    {
        $path_info = pathinfo($file);
        if(in_array($path_info['extension'], ['zip']))
        {
            return true;
        }
        return false;
    }
}
