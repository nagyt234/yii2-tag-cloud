<?php

namespace asu\tagcloud;

use yii\bootstrap\Widget;
use yii\helpers\Html;
use app\components\security\Role;

class TagCloud extends Widget {

    /**
     *
     * @var string The YiiTagCloud container css class
     */
    public $containerClass = 'YiiTagCloud';

    /**
     *
     * @var string The YiiTagCloud container html tag
     */
    public $containerTag = 'div';

    /**
     *
     * @var array options of the YiiTagCloud container
     */
    public $options = array();

    /**
     *
     * @var array with the tags
     */
    public $arrTags = array();

    /**
     *
     * @var string The begin color of the tags
     */
    public $beginColor = '000842';

    /**
     *
     * @var string The end color of the tags
     */
    public $endColor = 'A3AEFF';

    /**
     *
     * @var integer The smallest count (or occurrence).
     */
    public $minWeight = 1;

    /**
     *
     * @var integer The largest count (or occurrence).
     */
    public $maxWeight = 1;

    /**
     *
     * @var array the font-size colors
     */
    public $arrFontColor = array();

    /**
     *
     * @var integer The smallest font-size.
     */
    public $minFontSize = 8;

    /**
     *
     * @var integer The largest font-size.
     */
    public $maxFontSize = 36;

    /**
     *
     * @var string the URL of the CSS file
     */
    public $cssFile;

    public function init() {
        $this->options['id'] = $this->getId();
        
        if (!isset($this->options['class'])) {
            $this->options['class'] = $this->containerClass;
        }
        
        TagCloudAsset::register($this->getView());
        
        $this->getMinAndMaxWeight();
        $this->getFontSizes();
        $this->genereteColors();
    }

    public function run() {
        return $this->renderTagCloud();
    }

    public function renderTagCloud() {
        echo Html::beginTag($this->containerTag, $this->options);
        foreach ($this->arrTags as $tag => $conf) {
            $url = isset($conf['url']) ? $conf['url'] : 'javascript:return false';
            $options = isset($conf['options']) ? $conf['options'] : array();
            
            if (!isset($options['style']) || empty($options['style'])) {
                $options['style'] = 'font-size: ' . $conf['font-size'] . 'pt;' . 'color: ' . $this->arrFontColor[$conf['font-size']];
            }
            
            if (!isset($options['target']) || empty($options['target'])) {
                $options['target'] = '_blank';
            }
            
            @$options['class'] .= ' YiiTagCloudWord';
            
            // echo ' &nbsp;' . Html::link($tag, $url, $options) . '&nbsp; ';
            $adminSpecial = '';
            if (Role::isInRoleSiteAdmin()) {
                $adminSpecial = ' (' . $conf['weight'] . ')'; // . '['.$conf['font-size'].']';
            }
            echo ' &nbsp;' . Html::beginTag('span', $options) . $tag . $adminSpecial . Html::endTag('span') . '&nbsp; ';
        }
        echo Html::endTag($this->containerTag);
    }

    public function getMinAndMaxWeight() {
        foreach ($this->arrTags as $conf) {
            if ($this->minWeight > $conf['weight'])
                $this->minWeight = $conf['weight'];
            
            if ($this->maxWeight < $conf['weight'])
                $this->maxWeight = $conf['weight'];
        }
    }

    public function getFontSizes() {
        $countTags = count($this->arrTags);
        
        $count1 = round(($countTags / 100 * 12));
        $count2 = $count1 + round(($countTags / 100 * 13));
        $count3 = $count2 + round(($countTags / 100 * 25));
        $count4 = $countTags;
        
        $i = 1;
        foreach ($this->arrTags as &$conf) {
            
            if ($i <= $count1) {
                $conf['font-size'] = 15;
            } else if ($i <= $count2) {
                $conf['font-size'] = 13;
            } else if ($i <= $count3) {
                $conf['font-size'] = 11;
            } else {
                $conf['font-size'] = 9;
            }
            
            // $conf['font-size'] = $this->calcFontSize($conf['weight']);
            
            $this->arrFontColor[$conf['font-size']] = '';
            
            $i++;
        }
    }

    public function calcFontSize($weight) {
        $difference = $this->maxWeight - $this->minWeight;
        // Fix by alex start
        if ($this->maxWeight == $this->minWeight) {
            $difference = 1;
        }
        // Fix by alex end
        
        return round(((($weight - $this->minWeight) * ($this->maxFontSize - $this->minFontSize)) / ($difference)) + $this->minFontSize);
    }

    public function genereteColors() {
        krsort($this->arrFontColor);
        $beginColor = hexdec($this->beginColor);
        $endColor = hexdec($this->endColor);
        
        $R0 = ($beginColor & 0xff0000) >> 16;
        $G0 = ($beginColor & 0x00ff00) >> 8;
        $B0 = ($beginColor & 0x0000ff) >> 0;
        
        $R1 = ($endColor & 0xff0000) >> 16;
        $G1 = ($endColor & 0x00ff00) >> 8;
        $B1 = ($endColor & 0x0000ff) >> 0;
        
        $numColors = $theNumSteps = count($this->arrFontColor);
        
        $i = 0;
        foreach ($this->arrFontColor as &$value) {
            $R = $this->interpolate($R0, $R1, $i, $numColors);
            $G = $this->interpolate($G0, $G1, $i, $numColors);
            $B = $this->interpolate($B0, $B1, $i, $numColors);
            
            $value = sprintf("#%06X", (((($R << 8) | $G) << 8) | $B));
            
            $i++;
        }
    }

    public function interpolate($pBegin, $pEnd, $pStep, $pMax) {
        if ($pBegin < $pEnd)
            return (($pEnd - $pBegin) * ($pStep / $pMax)) + $pBegin;
        
        return (($pBegin - $pEnd) * (1 - ($pStep / $pMax))) + $pEnd;
    }

}
