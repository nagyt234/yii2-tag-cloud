<?php
namespace asu\tagcloud;

use yii\base\Widget;
use yii\helpers\Html;

class TagCloud extends Widget
{

    /**
     * Tags to display.
     * Every tag holds an name and a weight. Optional an url.
     *
     * [
     * "MVC" => ['weight' => 2],
     * "PHP" => ['weight' => 9, 'url' => 'http://php.net'],
     * "MySQL" => ['weight' => 8, 'url' => 'http://mysql.com'],
     * "jQuery" => ['weight' => 6]
     * ]
     *
     * @var array Tags.
     */
    public $tags = [];

    /**
     *
     * @var string Container tag.
     */
    public $containerTag = 'div';

    /**
     *
     * @var array Options of the container tag.
     */
    public $options = [];

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
     * If true, the weight of the word will be shown, otherwise not.
     *
     * @var boolean Display Weight.
     */
    public $displayWeight = false;

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
     * @var integer The smallest count.
     */
    private $minWeight = 1;

    /**
     *
     * @var integer The largest count.
     */
    private $maxWeight = 1;

    /**
     *
     * @var string Default css container class.
     */
    private $containerClass = 'tag-cloud';

    /**
     *
     * @var array the font-size colors
     */
    private $fontColors = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->options['id'] = $this->getId();
        
        if (! isset($this->options['class'])) {
            $this->options['class'] = $this->containerClass;
        }
        
        TagCloudAsset::register($this->getView());
        
        $this->calculateMinMaxWeight();
        $this->calculateFontSizes();
        $this->generateColors();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::beginTag($this->containerTag, $this->options);
        foreach ($this->tags as $tag => $conf) {
            $url = isset($conf['url']) ? $conf['url'] : 'javascript:return false';
            $options = isset($conf['options']) ? $conf['options'] : [];
            
            if (! isset($options['style']) || empty($options['style'])) {
                $options['style'] = 'font-size: ' . $conf['font-size'] . 'pt;' . 'color: ' . $this->fontColors[$conf['font-size']];
            }
            
            if (! isset($options['target']) || empty($options['target'])) {
                $options['target'] = '_blank';
            }
            
            if (! isset($options['class']) || empty($options['class'])) {
                $options['class'] = 'tag-cloud-word';
            }
            
            ($this->displayWeight) ? $weight = ' (' . $conf['weight'] . ')' : $weight = '';
            
            echo ' &nbsp;' . Html::a($tag . $weight, $url, $options) . '&nbsp; ';
        }
        echo Html::endTag($this->containerTag);
    }

    private function calculateFontSizes()
    {
        foreach ($this->tags as &$conf) {
            $conf['font-size'] = $this->calculateFontSizeByWeight($conf['weight']);
            $this->fontColors[$conf['font-size']] = '';
        }
    }

    private function calculateFontSizeByWeight($weight)
    {
        $difference = $this->maxWeight - $this->minWeight;
        if ($this->maxWeight == $this->minWeight) {
            $difference = 1;
        }
        return round(((($weight - $this->minWeight) * ($this->maxFontSize - $this->minFontSize)) / ($difference)) + $this->minFontSize);
    }

    private function generateColors()
    {
        krsort($this->fontColors);
        $beginColor = hexdec($this->beginColor);
        $endColor = hexdec($this->endColor);
        
        $R0 = ($beginColor & 0xff0000) >> 16;
        $G0 = ($beginColor & 0x00ff00) >> 8;
        $B0 = ($beginColor & 0x0000ff) >> 0;
        
        $R1 = ($endColor & 0xff0000) >> 16;
        $G1 = ($endColor & 0x00ff00) >> 8;
        $B1 = ($endColor & 0x0000ff) >> 0;
        
        $numColors = $theNumSteps = count($this->fontColors);
        
        $i = 0;
        foreach ($this->fontColors as &$value) {
            $R = $this->interpolate($R0, $R1, $i, $numColors);
            $G = $this->interpolate($G0, $G1, $i, $numColors);
            $B = $this->interpolate($B0, $B1, $i, $numColors);
            $value = sprintf("#%06X", (((($R << 8) | $G) << 8) | $B));
            $i ++;
        }
    }

    private function calculateMinMaxWeight()
    {
        foreach ($this->tags as $conf) {
            if ($this->minWeight > $conf['weight']) {
                $this->minWeight = $conf['weight'];
            }
            if ($this->maxWeight < $conf['weight']) {
                $this->maxWeight = $conf['weight'];
            }
        }
    }

    private function interpolate($begin, $end, $step, $max)
    {
        if ($begin < $end) {
            return (($end - $begin) * ($step / $max)) + $begin;
        }
        return (($begin - $end) * (1 - ($step / $max))) + $end;
    }
}
