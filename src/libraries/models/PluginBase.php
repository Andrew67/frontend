<?php
/**
 * PluginBase is the parent class for every plugin.
 *
 * @author Jaisen Mathai <jaisen@jmathai.com>
 */
class PluginBase extends BaseModel
{
  protected $plugin;
  private $pluginName, $pluginConf = null;
  public function __construct($params = null)
  {
    parent::__construct();
    $this->pluginName = preg_replace('/Plugin$/', '', get_class($this));
    if(isset($params['plugin']))
      $this->plugin = $params['plugin'];
    else
      $this->plugin = getPlugin();
  }

  public function defineConf()
  {
    return null;
  }

  public function getConf()
  {
    if($this->pluginConf !== null)
      return $this->pluginConf;

    $this->pluginConf = new stdClass;
    $conf = $this->plugin->loadConf($this->pluginName);
    foreach($conf as $name => $value)
      $this->pluginConf->$name = $value;

    return $this->pluginConf;
  }

  public function onAction($params = null) { }

  public function onView($params = null) { }

  public function renderHead($params = null) { }

  public function renderPhotoDetail($params = null) { }

  public function renderFooter($params = null) { }
}
