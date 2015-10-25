<?php

// Custom class to define a layout
// You can find more information about this on website:
// http://docs.slimframework.com/#Custom-Views

// Class CustomLayout
class CustomLayout extends \Slim\View{

	protected $_layout = NULL;

	public function set_layout($layout = NULL)
	{
		$this->_layout = $layout;
	}

	public function render($template, $data = NULL)
    {
        $templatePathname = $this->getTemplatesDirectory() . '/' . ltrim($template, '/');
        if (!is_file($templatePathname)) {
            throw new \RuntimeException("View cannot render `$template` because the template does not exist");
        }

        $data = array_merge($this->data->all(), (array) $data);
        extract($data);
        ob_start();
        require $templatePathname;

        $html = ob_get_clean();

        return $this->_render_layout($html, $data) ;
    }

    private function _render_layout($content, $data)
    {
    	if($this->_layout) 
    	{
    		$layout_path = $this->getTemplatesDirectory() . '/' . ltrim($this->_layout, '/');
    		if (!is_file($layout_path)) {
            	throw new \RuntimeException("View cannot render layout `$layout_path`. Layout does not exist");
        	}

        	$data = array_merge($this->data->all(), (array) $data);
        	extract($data);
        	ob_start();
	        require $layout_path;
	        $view = ob_get_clean();
	        
	        return $view;
    	} else {
    		return parent::render($template);
    	}

    	
    }
}