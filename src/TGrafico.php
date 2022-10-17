<?php

namespace AdiantiGraficos\Plugins\HighchartsPHP;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use AdiantiGraficos\Plugins\HighchartsPHP\HighchartOption;
use AdiantiGraficos\Plugins\HighchartsPHP\HighchartOptionRenderer;

class TGrafico extends TElement implements ArrayAccess
{
   const HIGHCHART = 0;
   const HIGHSTOCK = 1;
   const HIGHMAPS = 2;
   const ENGINE_JQUERY = 10;


   protected $callback;
   protected $withScriptTag;

   protected $_options = array();
   protected $_chartType;

   protected $_jsEngine;
   protected $_extraScripts = array();
   protected $_confs = array();


   public function __construct($chartType = self::HIGHCHART, $callback = null, $withScriptTag = false)
   {
      parent::__construct('div');
      $this->{'id'} = 'tgrafico_' . mt_rand(1000000000, 1999999999);
      $this->{'style'} = 'padding: 14px';

      self::__set('chart', array('renderTo' => $this->id));

      $this->_chartType =  is_null($chartType) ? self::HIGHCHART : $chartType;
      $this->_jsEngine = self::ENGINE_JQUERY;

      $this->callback = $callback;
      $this->withScriptTag = $withScriptTag;
      $this->setConfigurations();
   }


   public function setConfigurations($configurations = array())
   {
      include __DIR__ . DIRECTORY_SEPARATOR . "config.php";
      $this->_confs = array_replace_recursive($jsFiles, $configurations);
   }

   public function getScript()
   {
      $scripts = array();

      switch ($this->_chartType) {
         case self::HIGHCHART:
            $scripts[] = $this->_confs['highcharts']['path'] . $this->_confs['highcharts']['name'];
            break;

         case self::HIGHSTOCK:
            $scripts[] = $this->_confs['highstock']['path'] . $this->_confs['highstock']['name'];
            break;

         case self::HIGHMAPS:
            $scripts[] = $this->_confs['highmaps']['path'] . $this->_confs['highmaps']['name'];
            break;
      }

      return $scripts;
   }

   /**
    * Prints javascript script tags for all scripts that need to be included on page
    *
    * @param boolean $return if true it returns the scripts rather then echoing them
    */
   public function printScripts($return = false)
   {
      $scripts = '';
      foreach ($this->getScript() as $script) {
         $scripts .= '<script type="text/javascript" src="' . $script . '"></script>';
      }

      if ($return) {
         return $scripts;
      } else {
         echo $scripts;
      }
   }

   public function addExtraScript($key, $filepath, $filename)
   {
      $this->_confs['extra'][$key] = array('name' => $filename, 'path' => $filepath);
   }

   public function includeExtraScripts(array $keys = array())
   {
      $this->_extraScripts = empty($keys) ? array_keys($this->_confs['extra']) : $keys;
   }

   public static function setOptions($options)
   {
      //TODO: Check encoding errors
      $option = json_encode($options->getValue());
      return "Highcharts.setOptions($option);";
   }


   public function renderOptions()
   {
      return HighchartOptionRenderer::render($this->_options);
   }

   public function __clone()
   {
      foreach ($this->_options as $key => $value) {
         $this->_options[$key] = clone $value;
      }
   }


   public function __set($offset, $value)
   {
      $this->offsetSet($offset, $value);
      parent::__set($offset, $value);
   }

   public function __get($offset)
   {
      return $this->offsetGet($offset);
      parent::get($offset);
   }


   public function offsetExists($offset)
   {
      return isset($this->_options[$offset]);
   }

   public function offsetUnset($offset)
   {
      unset($this->_options[$offset]);
   }

   public function offsetSet($offset, $value)
   {
      $this->_options[$offset] = new HighchartOption($value);
   }

   public function offsetGet($offset)
   {
      if (!isset($this->_options[$offset])) {
         $this->_options[$offset] = new HighchartOption();
      }
      return $this->_options[$offset];
   }



   public function show()
   {
      $result = '';
      $result .= 'new Highcharts.';

      if ($this->_chartType === self::HIGHCHART) {
         $result .= 'Chart(';
      } else {
         $result .= 'StockChart(';
      }

      $result .= $this->renderOptions();
      $result .= is_null($this->callback) ? '' : ", $this->callback";
      $result .= ');';

      if ($this->withScriptTag) {
         $result = '<script type="text/javascript">' . $result . '</script>';
      }
      parent::show();
      TScript::create($result);
   }
}
