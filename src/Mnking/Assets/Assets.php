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

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = \Config::get('assets::config');
        $this->groups = $this->config['group'];
    }

    /**
     * Add new group to groups
     *
     * @param $group
     * @param $dir is a path to group or name of exists group
     * @return $this
     */
    public function addGroup($group, $dir)
    {
        if ($this->groupExists($dir)) {
            $dir = $this->groups[$dir];
        }
        $this->groups[$group] = $dir;
        return $this;
    }

    /**
     * Add new css to group
     *
     * @param $asset
     * @param string $group default is public
     * @return $this
     */
    public function addCss($asset,$group = 'public')
    {
        $this->add($asset,$group,'css');
        return $this;
    }

    /**
     * Add new javascript to group
     *
     * @param $asset
     * @param string $group default is public
     * @return $this
     */
    public function addJs($asset, $group = 'public')
    {
        $this->add($asset,$group,'js');
        return $this;
    }

    /**
     * Build the Link CSS tags.
     *
     * @param string $group
     * @return null|string
     */
    public function css($group = 'public')
    {
        if( ! $this->css)
            return null;

        $url = $this->buildLinkByGroup($group);
        $output = '';
        foreach($this->css[$group] as $file){

            if(!$this->isRemoteLink($file)){
                $file = $url.$file;
            }

            $output .= '<link rel="stylesheet" type="text/css" href="'.$file.'">'."\n";
        }

        return $output;
    }

    /**
     * Use to add sub dir to assets
     *
     * @param $dir
     * @return $this
     */
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
        foreach($this->js[$group] as $file){
            if(!$this->isRemoteLink($file)){
                $file = $url.$file;
            }
            $output .= '<script type="text/javascript" src="'.$file.'"></script>'."\n";
        }

        return $output;
    }

    /**
     * Every group has a link to run assets. Let build it for you
     *
     * @param $group
     * @return mixed
     */
    protected function buildLinkByGroup($group)
    {
        if(isset($this->groups[$group])){
            $dir = $this->groups[$group];
        }else{
            $dir = '/';
        }
        return \URL::to($dir);
    }

    /**
     * Determine group path has back dir
     *
     * @param $pattern
     * @return int
     */
    protected function isBackDir($pattern)
    {
        return preg_match('/../i',$pattern);
    }

    /**
     * Add css and js assets to array. Beside it add subdir in front of asset.
     * If asset is a remote link , not add subdir
     *
     * @param $asset
     * @param $group
     * @param $type
     */
    protected function add($asset,$group,$type)
    {
        if (is_array($asset)) {
            foreach ($asset as $item) {
                $this->add($item,$group,$type);
            }
            return;
        }
        $tmp = $this->$type;
        $tmp[$group][] = ($this->isRemoteLink($asset)? '': $this->groupDir) . $asset;
        $this->$type = $tmp;
    }

    /**
     * Determine a group is exists in groups
     *
     * @param $group
     * @return bool
     */
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