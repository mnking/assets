<?php
/**
 * Created by PhpStorm.
 * User: vuong
 * Date: 8/20/14
 * Time: 5:13 PM
 */

namespace Mnking\Assets;


class Assets
{

    private $groupDir;

    private $groups = [];

    private $css = [];

    private $js = [];

    private $config;

    public function test()
    {
        print_r($this->css);
        print_r($this->js);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = \Config::get('assets::config');
        $this->groups = $this->config['group'];
    }

    public function addGroup($group, $dir)
    {
        if ($this->groupExists($dir)) {
            $dir = $this->groups[$dir];
        }
        $this->groups[$group] = $dir;
        return $this;
    }

    public function addCss($asset,$group = 'public')
    {
        $this->add($asset,$group,'css');
        return $this;
    }

    public function addJs($asset, $group = 'public')
    {
        $this->add($asset,$group,'js');
        return $this;
    }

    public function css()
    {

    }

    public function dir($dir)
    {
        if($dir){
            $this->groupDir = '/' . $dir . '/';
        }
        return $this;
    }

    /**
     * Build the JavaScript script tags.
     *
     * @param $group
     * @return string
     */
    public function js($group = 'public')
    {
        if( ! $this->js)
            return null;

        $url = $this->buildLinkByGroup($group);
        $output = '';
        foreach($this->js[$group] as $file)
            $output .= '<script type="text/javascript" src="'.$url.$file.'"></script>'."\n";

        return $output;
    }

    protected function buildLinkByGroup($group)
    {
        if(isset($this->groups[$group])){
            $dir = $this->groups[$group];
            if($this->isBackDir($dir)){
                $dir = $this->makeCoreDir($dir);
            }
        }else{
            $dir = '/';
        }
        return \URL::to($dir);
    }

    protected function makeCoreDir($dir)
    {
        $dir = str_replace("../","",$dir);
        $source = base_path($dir);
        $des = public_path($this->config['core_dir']);
        File::copyDirectory($source, $des);
        return $this->config['core_dir'];
    }

    protected function isBackDir($pattern)
    {
        return preg_match('/../i',$pattern);
    }

    protected function add($asset,$group,$type)
    {
        if (is_array($asset)) {
            foreach ($asset as $item) {
                $this->add($item,$group,$type);
            }
            return;
        }
        $tmp = $this->$type;
        $tmp[$group][] = $this->groupDir . $asset;
        $this->$type = $tmp;
    }

    protected function groupExists($group)
    {
        return array_key_exists($group, $this->groups);
    }

    /**
     * Determine whether a link is local or remote.
     *
     * Undestands both "http://" and "https://" as well as protocol agnostic links "//"
     *
     * @param  string $link
     * @return bool
     */
    protected function isRemoteLink($link)
    {
        return ('http://' === substr($link, 0, 7) or 'https://' === substr($link, 0, 8) or '//' === substr($link, 0, 2));
    }
}