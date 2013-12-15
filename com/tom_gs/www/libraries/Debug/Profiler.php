<?php
/**
 * Profiler
 * 
 * @package libraries
 * @subpackage Debug
 * @author Tomohito Higuchi
 * @copyright http://www.tom-gs.com/
 * @date 2012.05.17
 */

namespace com\tom_gs\www\libraries\Debug;

class Profiler extends ValueDumper
{
    /**
     * @var Labels
     */
    private $labels = array(
        'memory_usage' => 'MEMORY USAGE',
        'microsec' => 'MICROTIME',
        'beginning_ms' => 'CUMULATIVE TIME',
        'last_ms' => 'TIME SINCE LAST',
        'last_microsec' => 'LAST MICROTIME',
        'date' => 'DATE',
        'line' => 'LINE',
        'file' => 'FILE',
        'info' => 'INFO',
    );
    
    /**
     * @var Beginning information
     */
    private $beginning_info = array();

    /**
     * @var array Dumping data
     */
    private $profiles = array();

    /**
     * Singleton
     */
    public static function getInstance()
    {
        static $instance;
        if ($instance === null) {
            $class_name = __CLASS__;
            $instance = new $class_name();
        }
        return $instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $line = $file = null;
        $debug_info = debug_backtrace();
        if (isset($debug_info[0])) {
            $line = $debug_info[0]['line'];
            $file = $debug_info[0]['file'];
        }

        $this->beginning_info = array(
            'memory_usage' => memory_get_usage(),
            'microsec' => microtime(true),
            //'beginning_ms' => $beginning_ms,
            //'last_ms' => $last_ms,
            'date' => date('Y-m-d H:i:s'),
            'line' => $line,
            'file' => $file,
            'info' => null,
        );
        parent::__construct();
    }

    /**
     * Add profile
     *
     * @param mixed $info
     */
    public function addProfile($info)
    {
        $last_index = count($this->profiles) - 1;
        if ($last_index < 0) {
            $last_index = 0;
        }

        $microsec = microtime(true);
        $beginning_ms = $microsec - $this->beginning_info['microsec'];
        $last_ms = 0;
        if ($last_index != 0) {
            $last_ms = $microsec - $this->profiles[$last_index]['last_microsec'];
        }

        $line = $file = null;
        $debug_info = debug_backtrace();
        if (isset($debug_info[0])) {
            $line = $debug_info[0]['line'];
            $file = $debug_info[0]['file'];
        }

        $profile = array(
            'memory_usage' => memory_get_usage(),
            'beginning_ms' => $beginning_ms,
            'last_ms' => $last_ms,
            'last_microsec' => $microsec,
            'date' => date('Y-m-d H:i:s'),
            'line' => $line,
            'file' => $file,
            'info' => htmlspecialchars(var_export($info, true)),
        );
        $this->profiles[] = $profile;
    }

    /**
     * Get JavaScript
     *
     * @return string JavaScript
     */
    public function getViewScripts()
    {
        echo "<script type=\"text/javascript\">
var DebugProfiler = function() {};
(function() {
  DebugProfilerHelper = {
    eventHandlerPrefix: '',
    eventListener: 'addEventListener',
    addEvent: function(obj, eventName, callback) {
      if (this.eventHandlerPrefix.length == 0) {
        obj[this.eventListener](this.eventHandlerPrefix + eventName, callback, false);
      } else {
        obj[this.eventListener](this.eventHandlerPrefix + eventName, callback);
      }
    },
    byId: function(id) {
      return document.getElementById(id);
    },
    extractNo: function(str) {
      var no = str.replace(/profile\-header\-no\-([0-9]+)/, '$1');
      return no;
    },
    init: function() {
      var dt = document.getElementsByTagName('dt');
      for (var i = 0, len = dt.length; i < len; i++) {
        if (dt[i].className.match(/profile\-header/)) {
          var no = this.extractNo(dt[i].id);
          var header = this.byId('profile-header-no-' + no);
          var body = this.byId('profile-body-no-' + no);
          this.addEvent(header, 'click', function(e) {DebugProfilerHelper.toggle(e)});
          body.style.visibility = 'hidden';
          body.style.display = 'none';
        }
      }
    },
    toggle: function(e) {
      e = e.target || e.srcElement;
      var no = this.extractNo(e.id);
      var target = this.byId('profile-body-no-' + no);
      if (target.style.visibility == 'hidden') {
        target.style.visibility = 'visible';
      } else {
        target.style.visibility = 'hidden';
      }
      if (target.style.display == 'none') {
        target.style.display = 'block';
      } else {
        target.style.display = 'none';
      }
    }
  }
  if (window.attachEvent) {
    DebugProfilerHelper.eventHandlerPrefix = 'on';
    DebugProfilerHelper.eventListener = 'attachEvent';
  }
}());
DebugProfilerHelper.addEvent(window, 'load', function() {
  DebugProfilerHelper.init()
});
</script>";
    }

    /**
     * Get styles
     *
     * @return string CSS
     */
    public function getViewStyles()
    {
        echo '<style type="text/css" rel="stylesheet">
.debug-profiler,
.debug-profiler table {
  border: 1px solid #999;
  color: #666;
  font-family: verdana;
  font-size: 10px;
}
.debug-profiler .title {
  background: #666;
  color: #fff;
  font-size: 16px;
  font-weight: bold;
}
.debug-profiler dl {
  margin: 0;
}
.debug-profiler dt {
  background: #bbb;
  border-top: 3px solid #999;
  font-weight: bold;
  margin: 0;
  padding: 3px 10px;
  cursor: pointer;
}
.debug-profiler dd {
  background: #eee;
  margin: 0;
  padding: 0 1em 1em 3em;
}
.debug-profiler table {
  background: #fff;
  border-collapse: collapse;
  width: 100%;
}
.debug-profiler table th {
  font-weight: bold;
  text-align: left;
  width: 120px;
}
.debug-profiler table th,
.debug-profiler table td {
  border: 1px solid #999;
  padding: 2px 3px;
}
</style>';
    }

    /**
     * Output profile
     */
    public function writeProfiles()
    {
        echo $this->getViewScripts();
        echo $this->getViewStyles();
        echo $this->dump(null);
    }

    /**
     * Write all strings with CSS
     * 
     * @param $text
     * @param Boolean $return = false
     * @param array $debug_info = array()
     */
    public function write($text, $return = false, array $debug_info = array())
    {
        $this->writeProfiles();
    }
    
    /**
     * Get label name
     *
     * @param string $name
     * @return string Label name
     */
    private function getLabelName($name)
    {
        if (array_key_exists($name, $this->labels)) {
            return $this->labels[$name];
        }
        return '';
    }

    /**
     * Create basic information
     *
     * @param array $profile
     * @return array(header, body)
     */
    private function createBasicInfo(array $profile)
    {
        $file = $line = 'undefined';
        if (isset($profile['file'])) {
            $file = $profile['file'];
        }
        if (isset($profile['line'])) {
            $line = $profile['line'];
        }
        $header = sprintf('%s on line %s', $file, $line);
        $body = '<table border="0"><tr>';
        foreach ($profile as $key => $value) {
            if ($key != 'file' && $key != 'line' && $key != 'info') {
                $body .= sprintf(
                    '<th class="label %s">%s</th><td>%s</td>',
                    $key,
                    $this->getLabelName($key),
                    $value
                );
            }
        }
        $body .= '</tr></table>';
        return array($header, $body);
    }

    /**
     * Get dumped string with CSS
     * 
     * @param $text
     * @param Boolean $return = false
     * @param array $debug_info = array()
     * @return String
     */
    public function dump($text, $return = false, array $debug_info = array())
    {
        list($header, $body) = $this->createBasicInfo($this->beginning_info);
        $result = $body;
        $result .= '<dl>';
        foreach ($this->profiles as $key => $profile) {
            list($header, $body) = $this->createBasicInfo($profile);
            $result .= '<dt class="profile-header" id="profile-header-no-' . $key . '">';
            $result .= $header;
            $result .= '</dt>';
            $result .= '<dd class="profile-body" id="profile-body-no-' . $key . '">';
            $result .= $body;
            $result .= sprintf(
                $this->getViewAreaFormat(),
                $this->createStyleValues($this->getViewAreaStyles()),
                $this->adjustStyle($profile['info'])
            );
            $result .= '</dd>' . "\n";
        }
        $result .= '</dl>';
        $result = sprintf(
            '<div class="debug-profiler"><div class="title">Profiler</div>%s</div>',
            $result
        );
        return $result;
    }
}
