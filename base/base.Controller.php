<?php 
 
class Controller {
    public $_view;
    public static $includes = array();

    public function add_include($include) {
        self::$includes[] = $include;
    }

    public function render($controller_name, $view_name, $params = array()) {
        $controller_file = CONTROLLER_PATH.'/controller.'.$controller_name.'.php';
        $controller_class= $controller_name.'_controller';
        $view_file = VIEW_PATH.'/view.'.strtolower($controller_name).'.'.$view_name.'.php';
    
        require_once($controller_file);
        $controller = new $controller_class;
 
        $controller->_view = $view_name;
 
        ob_start();
 
        $view = $controller->dispatch($params);

        extract((array)$view);
 
        if (is_file($view_file))
            include($view_file);
 
        $html = ob_get_clean();

        $html = $this->fill_includes($html);

        return $html;
    }

    public function dispatch($params) {
        $this->parameters = $params;
 
        if (method_exists($this, $this->_view))
              call_user_func(array($this, $this->_view));
 
        $defined_vars = get_defined_vars();
        $these_vars = array();
        foreach ($defined_vars['this'] as $key => $value)
             $these_vars[$key] = $value;
 
        return $these_vars;
    }

    private function fill_includes($html) {
        $js = $css = array();

        foreach (self::$includes as $include) {
            if (strpos($include, 'javascript'))
                $js[] = "<script src=\"{$include}\"></script>";
            else if (strpos($include, 'stylesheet'))
                $css[] = "<link rel=\"stylesheet\" href=\"{$include}\" />";
        }

        $html = str_replace('{{javascript_stuff}}', implode('', $js), $html);
        $html = str_replace('{{css_stuff}}', implode('', $css), $html);

        return $html;
    }
}

function render($controller_name, $view_name, $params = array()) {
    $controller = new Controller;
    return $controller->render($controller_name, $view_name, $params);
}