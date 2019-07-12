<?php
namespace Hhhzua\EncryptCompress;


use phpDocumentor\Reflection\Types\Integer;

class  EncryptCompress
{
    //包含数字
    const RULE_DIGIT = 1;
    //包含字母
    const RULE_LETTER = 2;
    //包含特殊字符
    const RULE_SPECIAL = 4;
    //随机
    const RULE_RANDOM = 8;

    //文件路径
    public $origin_file;

    //输出文件路径
    public $output_path;

    //密码长度
    public $length = 6;

    //压缩文件个数
    public $limit = 1;

    //失效时间
    public $expire_at = null;

    //密码规则
    public $rule = 0;

    //附加文件
    public $attach_file;

    //压缩后的文件
    public $zip_file = [];

    //是否加密
    public $is_encrypt = true;

    //密码
    public $password = '';

    //字符列表
    public $character = [
        [0,1,2,3,4,5,6,7,8,9],
        ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'],
        ['~','!','@','#','$','%','^','&','*','(',')','_','+',',','.',';',':','[',']','{','}','\\','|','`','"','\'']
    ];

    /**
     * EncryptCompress constructor.
     * @param $file_path
     * @param $rule
     * @param int $length
     * @param int $limit
     * @param null $expire_at
     */
    public function __construct(string $origin_file, string $output_path, $rule = 0, $length = 6, $limit = 1, $expire_at = null, $num = 1)
    {
        $this->origin_file = $origin_file;
        $this->length = $length;
        $this->rule = $rule;
        $this->limit = $limit;
        $this->expire_at = $expire_at;
        $this->output_path = $output_path;
        $this->num = $num;
    }

    public function test()
    {
        echo 'testing....success'.PHP_EOL;
    }

    /**
     * 运行环境检测
     */
    public function checkEnvironment()
    {
        return true;
    }

    /**
     * 设置不加密
     */
    public function setEncrypt($is_excrypt = true)
    {
        $this->is_encrypt = $is_excrypt;
    }

    /**
     * 添加附件
     * @param string $attach_path
     */
    public function attachFile($attach_path = '')
    {
        $this->attach_file = $attach_path;
    }

    /**
     * 执行
     */
    public function run()
    {
        try
        {
            $item = $this->num;
            while($item --)
            {
                $this->setZipPath(sprintf('%04d',$item));
                $this->compress($this->output_path);
            }
        }
        catch (\Exception $e)
        {
            print_r($e->getTraceAsString());
        }

    }

    /**
     * 设置压缩文件名
     * @return string
     */
    private function setZipPath($md5 = true, $number = 0)
    {
        if(is_dir(dirname($this->output_path)))
        {
            return $this->output_path;
        }

        $basename = dirname($this->origin_file);
        if($md5)
        {
            $this->output_path = $basename.'/'.md5_file($this->origin_file).'.zip';
        }
        elseif($number)
        {
            $this->output_path = $basename.'/'.pathinfo($this->output_path)['filename'].'_'.$number.'.zip';
        }
        else
        {
            $this->output_path = $basename.'/'.pathinfo($this->origin_file)['filename'].'.zip';
        }
    }

    /**
     * 执行压缩
     * @param string $file_name 压缩文件名称
     */
    private function compress($output_file = '')
    {
        //如果未设置输出文件吗则使用md5值
        if(!$output_file)
        {
            $output_file = md5_file($this->origin_file).'.zip';
        }

        $this->setPasword();

        $password = $this->is_encrypt && $this->password ? $this->password : null;

        ZipCompress::zip($this->origin_file, $output_file, $password);

        $this->zip_file[] = $output_file;
    }


    /**
     * 压缩是否生效
     * @return bool
     */
    public function isValid()
    {
        foreach ($this->zip_file as $file)
        {
            if(!file_exists($file))
            {
                return false;
            }
        }

        return true;
    }


    /**
     * 执行加密
     */
    public function encrypt()
    {

    }

    /**
     * 保存入库
     */
    public function save()
    {

    }

    /**
     * 设置密码
     */
    private function setPasword()
    {
        $password_arr = [];
        $rule_arr = array_reverse(str_split(decbin($this->rule)));
        $indexs = [];
        foreach ($rule_arr as $k=>$item)
        {
            if($item)
            {
                //数字
                $indexs[] = $k;
                if($k == self::RULE_RANDOM)
                {
                    $indexs = [self::RULE_DIGIT, self::RULE_LETTER, self::RULE_SPECIAL];
                }
            }
        }

        $length = $this->length;
        while($length --)
        {
            array_push($password_arr, $this->getRandomChat($indexs));
        }

        $this->password = implode('',$password_arr);
    }

    /**
     * 获取随机字符
     * @return mixed
     */
    public function getRandomChat($indexs = [0,1,2])
    {
        if(empty($indexs))
        {
            return '';
        }

        $chat_list = $this->character[$indexs[rand(0,count($indexs) - 1)]];
        return $chat_list[rand(0,count($chat_list) - 1)];
    }

}
